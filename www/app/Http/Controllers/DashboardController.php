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

        // Pizza por categoria (despesa mês atual)
        $categoryExpenses = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map->sum('amount');

        $categoryChartData = [
            'labels' => $categoryExpenses->keys()->values(),
            'series' => $categoryExpenses->values()->map(fn($v) => (float)$v)->values(),
        ];

        // Linha por mês (últimos 6 meses)
        $months = collect(range(5, 0))->map(function ($i) {
            return now()->copy()->subMonths($i);
        });

        $labels = [];
        $income = [];
        $expense = [];

        foreach ($months as $m) {
            $labels[] = $m->format('m/Y');

            $income[] = (float)Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->whereMonth('date', $m->month)
                ->whereYear('date', $m->year)
                ->sum('amount');

            $expense[] = (float)Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereMonth('date', $m->month)
                ->whereYear('date', $m->year)
                ->sum('amount');
        }

        $monthlyChartData = [
            'labels' => $labels,
            'income' => $income,
            'expense' => $expense,
        ];

        return view('dashboard', compact(
            'totalBalance',
            'monthIncome',
            'monthExpense',
            'lastTransactions',
            'goals',
            'categoryChartData',
            'monthlyChartData'
        ));
    }
}
