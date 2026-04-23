<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SampleAcceptance;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Kabul edilen + analizde + raporlanan numuneleri çektim 
        $acceptedSamples = SampleAcceptance::whereIn('status',['accepted','in_analysis','reported'])
            ->with('labProduct')
            ->latest()
            ->get();

        return view('pages.home', compact('acceptedSamples'));
    }
}
