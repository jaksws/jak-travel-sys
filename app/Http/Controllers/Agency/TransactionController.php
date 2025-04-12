<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index()
    {
        $transactions = Transaction::where('agency_id', auth()->user()->agency_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('agency.transactions.index', compact('transactions'));
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction)
    {
        // Check if transaction belongs to agency
        if ($transaction->agency_id != auth()->user()->agency_id) {
            abort(403, 'غير مصرح بالوصول إلى هذه المعاملة');
        }
        
        return view('agency.transactions.show', compact('transaction'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|string',
            'description' => 'required|string',
            'related_id' => 'nullable|integer',
            'related_type' => 'nullable|string',
        ]);

        $transaction = new Transaction();
        $transaction->amount = $validated['amount'];
        $transaction->type = $validated['type'];
        $transaction->description = $validated['description'];
        $transaction->agency_id = auth()->user()->agency_id;
        $transaction->user_id = auth()->id();
        
        if ($request->filled('related_id') && $request->filled('related_type')) {
            $transaction->related_id = $validated['related_id'];
            $transaction->related_type = $validated['related_type'];
        }

        $transaction->save();
        
        return redirect()->route('agency.transactions.index')
            ->with('success', 'تم إنشاء المعاملة بنجاح');
    }
}