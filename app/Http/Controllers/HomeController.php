<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SampleAcceptance;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $totalSamples    = SampleAcceptance::count();
        $reportedSamples = SampleAcceptance::where('status','reported')->count();
        $userCount       = User::count();

        $acceptedSamples = SampleAcceptance::whereIn('status',['accepted','in_analysis','reported'])
            ->with('labProduct')
            ->latest()
            ->get();

        return view('pages.home', compact(
            'totalSamples','reportedSamples','userCount','acceptedSamples'
        ));
    }
}
