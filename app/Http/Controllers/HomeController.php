<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;

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
        
        // Fetch active services for public view
        $services = Service::where('status', 'active')->orderBy('type')->get()->groupBy('type');
        
        // Otherwise show welcome page with services
        return view('welcome', compact('services'));
    }

    /**
     * Show the public services page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function publicServices()
    {
        // Fetch active services for public view
        $services = Service::where('status', 'active')->orderBy('type')->get()->groupBy('type');
        
        // Show public services page
        return view('public.services', compact('services'));
    }
}
