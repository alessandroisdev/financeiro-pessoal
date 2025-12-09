<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Transações</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1, h2, h3 {
            margin: 0 0 10px;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .filters {
            margin-bottom: 15px;
            font-size: 11px;
        }

        .filters strong {
            display: inline-block;
            width: 120px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #666;
            padding: 6px 4px;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .income {
            color: #0a7d0a;
            font-weight: bold;
        }

        .expense {
            color: #b30000;
            font-weight: bold;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Relatório de Transações</h1>
    <small>Gerado em {{ now()->format('d/m/Y H:i') }}</small>
</div>

{{-- FILTROS UTILIZADOS --}}
@if(!empty($filters))
    <div class="filters">
        <h3>Filtros aplicados</h3>

        @if(!empty($filters['date_start']))
            <div><strong>Data inicial:</strong> {{ \Carbon\Carbon::parse($filters['date_start'])->format('d/m/Y') }}
            </div>
        @endif

        @if(!empty($filters['date_end']))
            <div><strong>Data final:</strong> {{ \Carbon\Carbon::parse($filters['date_end'])->format('d/m/Y') }}</div>
        @endif

        @if(!empty($filters['account_id']))
            @php
                $acc = \App\Models\Account::find($filters['account_id']);
            @endphp
            <div><strong>Conta:</strong> {{ $acc?->name }}</div>
        @endif

        @if(!empty($filters['category_id']))
            @php
                $cat = \App\Models\Category::find($filters['category_id']);
            @endphp
            <div><strong>Categoria:</strong> {{ $cat?->name }}</div>
        @endif

        @if(!empty($filters['type']))
            <div>
                <strong>Tipo:</strong>
                {{ $filters['type'] === 'income' ? 'Receita' : 'Despesa' }}
            </div>
        @endif
    </div>
@endif

{{-- TABELA PRINCIPAL --}}
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Data</th>
        <th>Conta</th>
        <th>Categoria</th>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Status</th>
        <th>Descrição</th>
    </tr>
    </thead>
    <tbody>

    @forelse($transactions as $t)
        <tr>
            <td>{{ $t->id }}</td>

            <td>{{ $t->date->format('d/m/Y') }}</td>

            <td>{{ $t->account->name ?? '-' }}</td>

            <td>{{ $t->category->name ?? '-' }}</td>

            <td class="{{ $t->type === 'income' ? 'income' : 'expense' }}">
                {{ $t->type === 'income' ? 'Receita' : 'Despesa' }}
            </td>

            <td>
                R$ {{ number_format($t->amount, 2, ',', '.') }}
            </td>

            <td>
                {{
                    [
                        'pending'  => 'Pendente',
                        'paid'     => 'Pago',
                        'canceled' => 'Cancelado'
                    ][$t->status] ?? $t->status
                }}
            </td>

            <td>{{ $t->description }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="text-align:center;">Nenhuma transação encontrada.</td>
        </tr>
    @endforelse

    </tbody>
</table>

<div class="footer">
    Relatório financeiro — {{ config('app.name') }}
</div>

</body>
</html>
