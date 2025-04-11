<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Request as TravelRequest;
use App\Notifications\QuoteStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuoteController extends Controller
{
    /**
     * Store a newly created quote in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:requests,id',
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'description' => 'required|string',
            'valid_until' => 'required|date|after:today'
        ]);

        $travelRequest = TravelRequest::findOrFail($request->request_id);
        
        // Check if user has permission to create a quote for this request
        if (Auth::user()->role === 'subagent') {
            // Check if the subagent belongs to the same agency as the request
            if (Auth::user()->agency_id !== $travelRequest->agency_id) {
                abort(403, 'غير مصرح لك بتقديم عروض لهذا الطلب');
            }
        } else {
            abort(403, 'غير مصرح لك بإنشاء عروض أسعار');
        }

        $quote = Quote::create([
            'request_id' => $travelRequest->id,
            'user_id' => Auth::id(),
            'subagent_id' => Auth::id(),
            'price' => $request->price,
            'currency_id' => $request->currency_id,
            'description' => $request->description,
            'status' => 'pending',
            'valid_until' => $request->valid_until
        ]);

        return redirect()->route('quotes.show', $quote)
                         ->with('success', 'تم إنشاء عرض السعر بنجاح وإرساله للمراجعة');
    }

    /**
     * Display the specified quote.
     */
    public function show(Quote $quote)
    {
        // Check access permissions
        if (Auth::id() !== $quote->user_id && 
            Auth::id() !== $quote->request->user_id &&
            Auth::user()->agency_id !== $quote->request->agency_id &&
            !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }

        $quote->load(['request.service', 'user', 'currency']);
        
        return view('quotes.show', compact('quote'));
    }

    /**
     * Accept a quote.
     */
    public function accept(Quote $quote)
    {
        // Check if the user is the client who owns the request
        if (Auth::id() !== $quote->request->user_id) {
            abort(403, 'غير مصرح لك بقبول هذا العرض');
        }

        // Check if the quote status allows acceptance
        if ($quote->status !== 'pending' && $quote->status !== 'agency_approved') {
            return redirect()->route('quotes.show', $quote)
                          ->with('error', 'لا يمكن قبول هذا العرض في حالته الحالية');
        }

        // Update quote status
        $quote->update(['status' => 'accepted']);

        // Update request status
        $quote->request->update(['status' => 'approved']);

        // Send notification - Fix for null user reference
        try {
            // Check if subagent_id exists instead of user_id
            if ($quote->subagent_id && $quote->subagent) {
                $quote->subagent->notify(new QuoteStatusChanged($quote, 'accepted'));
            } else {
                Log::warning('No valid user found for notification on quote #' . $quote->id);
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error sending notification: ' . $e->getMessage());
        }

        return redirect()->route('quotes.show', $quote)
                       ->with('success', 'تم قبول عرض السعر بنجاح');
    }

    /**
     * Reject a quote.
     */
    public function reject(Quote $quote)
    {
        // Check if the user is the client who owns the request
        if (Auth::id() !== $quote->request->user_id) {
            abort(403, 'غير مصرح لك برفض هذا العرض');
        }

        // Check if the quote status allows rejection
        if ($quote->status !== 'pending' && $quote->status !== 'agency_approved') {
            return redirect()->route('quotes.show', $quote)
                          ->with('error', 'لا يمكن رفض هذا العرض في حالته الحالية');
        }

        // Update quote status
        $quote->update(['status' => 'rejected']);

        // Send notification - Fix for null user reference
        try {
            // Check if subagent_id exists instead of user_id
            if ($quote->subagent_id && $quote->subagent) {
                $quote->subagent->notify(new QuoteStatusChanged($quote, 'rejected'));
            } else {
                Log::warning('No valid user found for notification on quote #' . $quote->id);
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error sending notification: ' . $e->getMessage());
        }

        return redirect()->route('quotes.show', $quote)
                       ->with('success', 'تم رفض عرض السعر بنجاح');
    }
}