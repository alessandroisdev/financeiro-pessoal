<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Goal;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalBalance = Account::where('user_id', $user->id)->sum('current_balance');

        $monthIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $monthExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $lastTransactions = Transaction::with(['account', 'category'])
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        $goals = Goal::ofUser()
            ->where('month', Carbon::now()->month)
            ->where('year', Carbon::now()->year)
            ->with('category')
            ->get();

        return view('dashboard', compact(
            'totalBalance', 'monthIncome', 'monthExpense', 'lastTransactions', 'goals'
        ));
    }
}
