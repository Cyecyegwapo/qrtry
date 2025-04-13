<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // After successful login, redirect users based on their role
        if (auth()->user()->isAdmin()) {
            return redirect()->route('events.index'); // Redirect admin to events index
        } else {
            return redirect()->route('events.index'); // Redirect regular users to events index
        }
    }
}
