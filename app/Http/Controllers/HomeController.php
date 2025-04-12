<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Allow public access to home page
    }

    /**
     * Show the application home or redirect to appropriate dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // If user is logged in, redirect to appropriate dashboard
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isAgency()) {
                return redirect()->route('agency.dashboard');
            } elseif ($user->isSubagent()) {
                return redirect()->route('subagent.dashboard');
            } elseif ($user->isCustomer()) {
                return redirect()->route('customer.dashboard');
            }
        }
        
        // Otherwise show welcome page
        return view('welcome');
    }
}
