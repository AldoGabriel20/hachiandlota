<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $slides = Slide::where("status", 1)->get()->take(3);
        $sproducts = Product::
            whereColumn('sale_price', '<', 'regular_price')
            ->where('sale_price', '<>', 'regular_price')
            ->where('sale_price', '<>', '')->inRandomOrder()->get()->take(8);
        $fproducts = Product::where('featured', 1)->get()->take(8);
        return view('index', compact('slides', 'sproducts', 'fproducts'));
    }
}
