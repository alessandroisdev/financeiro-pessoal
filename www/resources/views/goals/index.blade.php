@extends('layouts.app')

@section('title','Metas')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h1>Metas Mensais</h1>
            <a href="{{ route('goals.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nova Meta
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Categoria</th>
                <th>Mês/Ano</th>
                <th>Valor da Meta</th>
                <th>Gasto Atual</th>
                <th>Progresso</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($goals as $g)

                @php
                    $currentSpent = \App\Models\Transaction::where('user_id', auth()->id())
                        ->where('category_id', $g->category_id)
                        ->where('type', 'expense')
                        ->whereMonth('date', $g->month)
                        ->whereYear('date', $g->year)
                        ->sum('amount');

                    $percent = $g->amount > 0 ? ($currentSpent / $g->amount) * 100 : 0;

                    $color = $percent >= 100 ? 'danger' :
                             ($percent >= 70 ? 'warning' : 'success');
                @endphp

                <tr>
                    <td>{{ $g->category->name }}</td>
                    <td>{{ str_pad($g->month, 2, '0', STR_PAD_LEFT) }}/{{ $g->year }}</td>
                    <td>R$ {{ number_format($g->amount, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($currentSpent, 2, ',', '.') }}</td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar bg-{{ $color }}"
                                 style="width: {{ min($percent, 100) }}%">
                                {{ number_format($percent, 0) }}%
                            </div>
                        </div>
                    </td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('goals.edit', $g) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form method="POST" action="{{ route('goals.destroy', $g) }}"
                              onsubmit="return confirm('Deseja excluir esta meta?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>
    </div>
@endsection
