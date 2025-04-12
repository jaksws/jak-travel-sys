<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DataFixController extends Controller
{
    /**
     * Handler to create dummy views for tests
     */
    public function getView($viewName)
    {
        // Special handling for admin requests view
        if ($viewName === 'admin.requests.index') {
            $requests = \App\Models\Request::select(['id', 'title', 'status'])
                ->with(['service', 'user'])
                ->latest()
                ->paginate(15);
            
            return view($viewName, ['requests' => $requests]);
        }
        
        // Create dummy views for testing purposes
        if (!View::exists($viewName)) {
            // For testing, we'll return a simple view
            return view('welcome')->with('viewName', $viewName);
        }
        
        return view($viewName);
    }
}
