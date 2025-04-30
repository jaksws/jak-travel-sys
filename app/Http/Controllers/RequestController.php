<?php

namespace App\Http\Controllers;

use App\Models\Request as ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    /**
     * Display a listing of the requests for agents.
     */
    public function index(Request $request)
    {
        // For agents, show requests related to their agency
        $query = ServiceRequest::query();
        
        if (Auth::user()->isAgent()) {
            $query->where('agency_id', Auth::user()->agency_id);
        } else {
            abort(403, 'Unauthorized');
        }
        
        $requests = $query->with(['service', 'user'])->latest()->paginate(15);
        
        return view('agent.requests.index', compact('requests'));
    }
    
    /**
     * Display a listing of all requests for admins.
     */
    public function adminIndex(Request $request)
    {
        // For admins, show all requests
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        
        $requests = ServiceRequest::with(['service', 'user'])->latest()->paginate(15);
        
        return view('admin.requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new request.
     */
    public function create()
    {
        return view('requests.create');
    }

    /**
     * Store a newly created request in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id'    => 'required|exists:services,id',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'required_date' => 'nullable|date',
            'notes'         => 'nullable|string',
        ]);


        $service = Service::findOrFail($request->service_id);
        
        $serviceRequest = ServiceRequest::create([
            'service_id' => $service->id,
            'user_id' => Auth::id(),
            'agency_id' => $service->agency_id,
            'title' => $request->title,
            'description' => $request->description,
            'required_date' => $request->required_date,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return redirect()->route('requests.show', $serviceRequest)
                         ->with('success', 'تم إنشاء طلبك بنجاح. سيتم التواصل معك قريباً');

    }

    /**
     * Display the specified request.
     */
    public function show(ServiceRequest $request)
    {
        return view('requests.show', ['request' => $request]);
    }

    /**
     * Update the specified request in storage.
     */
    public function update(Request $httpRequest, TravelRequest $request)
    {
        if ($request->user_id !== Auth::id() || $request->status !== 'pending') {
            abort(403);
        }

        $data = $httpRequest->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'required_date' => 'required|date',
            'notes'         => 'nullable|string',
        ]);

        $request->update($data);

        return redirect()->route('requests.show', $request);
    }
}
