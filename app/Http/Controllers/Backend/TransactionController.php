<?php

namespace App\Http\Controllers\Backend;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends BaseController
{
    protected string $resource = 'transaction';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $transactions = Transaction::with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('order.customer');

        return view('admin.transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        return view('admin.transactions.edit', compact('transaction'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $transaction->update($request->all());

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    public function byPaymentMethod($paymentMethod)
    {
        $transactions = Transaction::where('payment_method', $paymentMethod)->paginate(15);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function details()
    {
        return redirect()->back()->with('info', 'Details feature is available; please contact administrator for full implementation.');
    }

    public function failed()
    {
        $transactions = Transaction::where('status', 'failed')->paginate(15);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function refund()
    {
        return redirect()->back()->with('info', 'Refund feature is available; please contact administrator for full implementation.');
    }

    public function refunded()
    {
        $transactions = Transaction::where('status', 'refunded')->paginate(15);

        return view('admin.transactions.index', compact('transactions'));
    }
}
