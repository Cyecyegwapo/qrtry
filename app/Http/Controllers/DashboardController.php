<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View; // Import the View class

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View // Add this index method
    {
        // Make sure you have a 'dashboard.blade.php' view file
        return view('dashboard');
    }
}