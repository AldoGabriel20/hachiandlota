<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use App\Models\Product;
use App\Models\Contact;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class HomeController extends Controller
{
    public function index()
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }

        $slides = Slide::where("status", 1)->get()->take(3);
        $sproducts = Product::
            whereColumn('sale_price', '<', 'regular_price')
            ->where('sale_price', '<>', 'regular_price')
            ->where('sale_price', '<>', '')->inRandomOrder()->get()->take(8);
        $fproducts = Product::where('featured', 1)->get()->take(8);

        if (Auth::check()) {
            Cart::instance('cart')->restore(Auth::user()->email);
            Cart::instance('wishlist')->restore(Auth::user()->email);
        }

        return view('index', compact('slides', 'sproducts', 'fproducts'));
    }

    public function contact()
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }

        return view('contact');
    }

    public function contact_store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email',
            'phone' => 'required|numeric|regex:/^[0-9]{10,12}$/',
            'comment' => 'required'
        ]);

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->comment = $request->comment;
        $contact->save();
        return redirect()->back()->with('success', 'Your message has been sent successfully');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name', 'LIKE', "%{$query}%")->get()->take(8);
        return response()->json($results);
    }

    public function about()
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }

        return view('about');
    }
}
