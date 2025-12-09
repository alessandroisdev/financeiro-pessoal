<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $q = Transaction::query()
            ->with(['account', 'category'])
            ->where('user_id', auth()->id());

        if (!empty($this->filters['date_start'])) {
            $q->whereDate('date', '>=', $this->filters['date_start']);
        }
        if (!empty($this->filters['date_end'])) {
            $q->whereDate('date', '<=', $this->filters['date_end']);
        }
        if (!empty($this->filters['account_id'])) {
            $q->where('account_id', $this->filters['account_id']);
        }
        if (!empty($this->filters['category_id'])) {
            $q->where('category_id', $this->filters['category_id']);
        }
        if (!empty($this->filters['type'])) {
            $q->where('type', $this->filters['type']);
        }

        return $q;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Data',
            'Conta',
            'Categoria',
            'Tipo',
            'Valor',
            'Status',
            'Descrição',
        ];
    }

    public function map($t): array
    {
        return [
            $t->id,
            $t->date->format('d/m/Y'),
            $t->account->name ?? '',
            $t->category->name ?? '',
            $t->type === 'income' ? 'Receita' : 'Despesa',
            number_format($t->amount, 2, ',', '.'),
            $t->status,
            $t->description,
        ];
    }
}

