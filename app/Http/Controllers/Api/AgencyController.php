<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    /**
     * Display a listing of agencies
     */
    public function index(Request $request)
    {
        $agencies = Agency::where('status', 'active')
            ->with(['services' => function($query) {
                $query->where('status', 'active');
            }])
            ->paginate($request->per_page ?? 10);
            
        return response()->json($agencies);
    }
    
    /**
     * Display details for a specific agency
     */
    public function show(Agency $agency)
    {
        // Load active services for this agency
        $agency->load([
            'services' => function($query) {
                $query->where('status', 'active')
                      ->orderBy('created_at', 'desc');
            }
        ]);
        
        return response()->json([
            'data' => $agency
        ]);
    }
}