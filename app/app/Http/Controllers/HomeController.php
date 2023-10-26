<?php

namespace App\Http\Controllers;

use Bo\Car\Models\Car;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $car_news = Car::where('status', Car::PUBLISH_STATUS)->orderBy('created_at', 'DESC')->limit(4)->get();

        return view('home.index', [
            'car_news' => $car_news,
        ]);
    }
}
