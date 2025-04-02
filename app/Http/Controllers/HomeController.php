<?php

namespace App\Http\Controllers;

use App\Models\Slide;

class HomeController extends Controller
{
    public function index()
    {
        $slides = Slide::where("status", 1)->get()->take(3);
        return view('index', compact('slides'));
    }
}
