<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Models\Account;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function transactionsForm()
    {
        // Reaproveita accounts/categories para filtros
        $accounts = Account::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->get();

        return view('reports.transactions-form', compact('accounts','categories'));
    }

    public function transactionsExcel(Request $request)
    {
        $filters = $request->only(['date_start','date_end','account_id','category_id','type']);

        return Excel::download(new TransactionsExport($filters), 'transacoes.xlsx');
    }

    public function transactionsPdf(Request $request)
    {
        $filters = $request->only(['date_start','date_end','account_id','category_id','type']);

        $q = (new TransactionsExport($filters))->query()->get();

        $pdf = Pdf::loadView('reports.transactions-pdf', [
            'transactions' => $q,
            'filters'      => $filters,
        ]);

        return $pdf->download('transacoes.pdf');
    }
}

