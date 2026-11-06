<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $query = Rating::with(['order.customer', 'driver.user'])->latest();

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $ratings = $query->paginate(15)->withQueryString();

        $avgRating = round((float) Rating::avg('rating'), 1) ?: 5.0;
        $totalReviews = Rating::count();
        $fiveStarCount = Rating::where('rating', 5)->count();
        $fourStarCount = Rating::where('rating', 4)->count();

        return view('admin.ratings.index', compact(
            'ratings',
            'avgRating',
            'totalReviews',
            'fiveStarCount',
            'fourStarCount'
        ));
    }
}

