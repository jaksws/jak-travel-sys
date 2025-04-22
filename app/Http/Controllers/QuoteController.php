<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\Request as TravelRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\QuoteStatusChanged;

class QuoteController extends Controller
{
    // Store new quote from subagent
    public function store(Request $request)
    {
        if (!Auth::user()->isSubAgent()) {
            abort(403);
        }

        $data = $request->validate([
            'request_id'  => 'required|exists:requests,id',
            'price'       => 'required|numeric',
            'currency_id' => 'required|exists:currencies,id',
            'description' => 'nullable|string',
            'valid_until' => 'required|date',
        ]);

        $data['user_id'] = Auth::id();
        $data['status']  = 'pending';

        Quote::create($data);

        return redirect()->back();
    }

    // Show quote for authorized users
    public function show(Quote $quote)
    {
        return view('quotes.show', compact('quote'));
    }

    // Update quote by subagent
    public function update(Request $request, Quote $quote)
    {
        if (!Auth::user()->isSubAgent() || $quote->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'price'       => 'required|numeric',
            'description' => 'nullable|string',
            'valid_until' => 'required|date',
        ]);

        $quote->update($data);

        return redirect()->back();
    }

    // Client accepts quote
    public function accept(Quote $quote)
    {
        $user = Auth::user();
        if (!$user->isClient() || $quote->request->user_id !== $user->id) {
            abort(403);
        }

        $quote->status = 'accepted';
        $quote->save();

        $rq = $quote->request;
        $rq->status = 'approved';
        $rq->save();

        Notification::send(
            [$quote->user],
            new QuoteStatusChanged($quote, 'accepted')
        );

        return redirect()->back();
    }

    // Client rejects quote
    public function reject(Quote $quote)
    {
        $user = Auth::user();
        if (!$user->isClient() || $quote->request->user_id !== $user->id) {
            abort(403);
        }

        $quote->status = 'rejected';
        $quote->save();

        Notification::send(
            [$quote->user],
            new QuoteStatusChanged($quote, 'rejected')
        );

        return redirect()->back();
    }
}