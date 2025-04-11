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
        // Create dummy views for testing purposes
        if (!View::exists($viewName)) {
            // For testing, we'll return a simple view
            return view('welcome')->with('viewName', $viewName);
        }
        
        return view($viewName);
    }
}
