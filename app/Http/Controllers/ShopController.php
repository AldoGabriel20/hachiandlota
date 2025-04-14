<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }

        $size = $request->query('size') ? $request->query('size') : 12;
        $o_column = "";
        $o_order = "";
        $order = $request->query('order') ? $request->query('order') : -1;
        $f_categories = $request->query('categories');
        $min_price = $request->query('min') ? $request->query('min') : 1000;
        $max_price = $request->query('max') ? $request->query('max') : 1000000;

        switch ($order) {
            case 1:
                $o_column = 'created_at';
                $o_order = 'DESC';
                break;
            case 2:
                $o_column = 'created_at';
                $o_order = 'ASC';
                break;
            case 3:
                $o_column = 'sale_price';
                $o_order = 'ASC';
                break;
            case 4:
                $o_column = 'sale_price';
                $o_order = 'DESC';
                break;
            default:
                $o_column = 'id';
                $o_order = 'DESC';
        }

        $categories = Category::orderBy('name', 'ASC')->get();
        $products = Product::where(function ($query) use ($f_categories) {
            $query->whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'" . $f_categories . "'=''");
        })
            ->where(function ($query) use ($min_price, $max_price) {
                $query->whereBetween('regular_price', [$min_price, $max_price])
                    ->orWhereBetween('sale_price', [$min_price, $max_price]);
            })
            ->orderBy($o_column, $o_order)->paginate($size);

        if (Auth::check()) {
            Cart::instance('cart')->store(Auth::user()->email);
            Cart::instance('wishlist')->store(Auth::user()->email);
        }

        return view('shop', compact('products', 'size', 'order', 'categories', 'f_categories', 'min_price', 'max_price'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $rproducts = Product::where('slug', '<>', $product_slug)->get()->take(8);
        return view('details', compact('product', 'rproducts'));
    }
}
