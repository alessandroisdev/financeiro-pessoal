<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return view('transactions.index');
    }

    public function datatable(Request $request)
    {
        $query = Transaction::query()
            ->with(['account', 'category'])
            ->where('user_id', auth()->id());

        if ($request->filled('date_start')) {
            $query->whereDate('date', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('date', '<=', $request->date_end);
        }
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return datatables()->eloquent($query)
            ->addColumn('account_name', fn($t) => $t->account->name)
            ->addColumn('category_name', fn($t) => $t->category->name)
            ->addColumn('formatted_amount', fn($t) =>
                'R$ '.number_format($t->amount, 2, ',', '.')
            )
            ->addColumn('formatted_date', fn($t) =>
            $t->date->format('d/m/Y')
            )
            ->addColumn('actions', function ($t) {
                return view('components.table-actions', [
                    'edit' => route('transactions.edit', $t),
                    'delete' => route('transactions.destroy', $t),
                ])->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        return view('transactions.create', [
            'accounts' => Account::where('user_id', auth()->id())->get(),
            'categories' => Category::where('user_id', auth()->id())->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'is_recurring' => 'nullable|boolean',
            'status' => 'required|in:pending,paid,canceled'
        ]);

        $data['user_id'] = auth()->id();

        Transaction::create($data);

        return redirect()->route('transactions.index')->with('success', 'Lançamento criado.');
    }

    public function edit(Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);

        return view('transactions.edit', [
            'transaction' => $transaction,
            'accounts' => Account::where('user_id', auth()->id())->get(),
            'categories' => Category::where('user_id', auth()->id())->get()
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'is_recurring' => 'nullable|boolean',
            'status' => 'required|in:pending,paid,canceled'
        ]);

        $transaction->update($data);

        return redirect()->route('transactions.index')->with('success', 'Lançamento atualizado.');
    }

    public function destroy(Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Lançamento removido.');
    }
}
