<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\TariffSetting;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $tariff = TariffSetting::current();
        $vehicles = Vehicle::where('status', 'Ready')->take(4)->get();
        $recentRatings = Rating::with('customer')->where('rating', '>=', 4)->latest()->take(4)->get();

        return view('welcome', compact('tariff', 'vehicles', 'recentRatings'));
    }
}

