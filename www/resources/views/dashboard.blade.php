@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <h1 class="mb-4">Visão geral</h1>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-success h-100">
                    <div class="card-body">
                        <h5 class="card-title">Saldo total</h5>
                        <p class="card-text fs-4 fw-bold text-success">
                            R$ {{ number_format($totalBalance, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-primary h-100">
                    <div class="card-body">
                        <h5 class="card-title">Receitas no mês</h5>
                        <p class="card-text fs-4 fw-bold text-primary">
                            R$ {{ number_format($monthIncome, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-danger h-100">
                    <div class="card-body">
                        <h5 class="card-title">Despesas no mês</h5>
                        <p class="card-text fs-4 fw-bold text-danger">
                            R$ {{ number_format($monthExpense, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <h3>Despesas por categoria (mês)</h3>
                <div id="chart-by-category"></div>
            </div>
            <div class="col-md-6">
                <h3>Receitas x Despesas (últimos meses)</h3>
                <div id="chart-by-month"></div>
            </div>
        </div>

        @if(!empty($goals))
            <h2 class="mt-5">Metas do mês</h2>

            <div class="row g-3">
                @foreach($goals as $g)
                    @php
                        $currentSpent = \App\Models\Transaction::where('user_id', auth()->id())
                            ->where('category_id', $g->category_id)
                            ->where('type','expense')
                            ->whereMonth('date', now()->month)
                            ->whereYear('date', now()->year)
                            ->sum('amount');

                        $percent = $g->amount > 0 ? ($currentSpent / $g->amount) * 100 : 0;
                        $color = $percent >= 100 ? 'danger' : ($percent >= 70 ? 'warning' : 'success');
                    @endphp

                    <div class="col-md-4">
                        <div class="card h-100 border-{{ $color }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $g->category->name }}</h5>

                                <p class="fw-bold">
                                    {{ number_format($percent, 0) }}% da meta
                                </p>

                                <div class="progress mb-2">
                                    <div class="progress-bar bg-{{ $color }}"
                                         style="width: {{ min($percent, 100) }}%"></div>
                                </div>

                                <small>
                                    Meta: R$ {{ number_format($g->amount,2,',','.') }}<br>
                                    Gasto: R$ {{ number_format($currentSpent,2,',','.') }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if(!empty($lastTransactions))
            <h2 class="mb-3">Últimos lançamentos</h2>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Conta</th>
                    <th>Categoria</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                </tr>
                </thead>
                <tbody>
                @forelse($lastTransactions as $t)
                    <tr>
                        <td>{{ $t->date->format('d/m/Y') }}</td>
                        <td>{{ $t->account->name }}</td>
                        <td>{{ $t->category->name }}</td>
                        <td>{{ $t->type === 'income' ? 'Receita' : 'Despesa' }}</td>
                        <td>
                            R$ {{ number_format($t->amount, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Nenhum lançamento recente.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        const categoryChartData = @json($categoryChartData);
        const monthlyChartData = @json($monthlyChartData);

        // Pizza por categoria (despesa mês corrente)
        const categoryChart = new ApexCharts(document.querySelector("#chart-by-category"), {
            chart: {type: 'donut'},
            labels: categoryChartData.labels,
            series: categoryChartData.series,
            legend: {position: 'bottom'}
        });
        categoryChart.render();

        // Linha por mês (receita x despesa últimos 6/12 meses)
        const monthlyChart = new ApexCharts(document.querySelector("#chart-by-month"), {
            chart: {type: 'line'},
            xaxis: {categories: monthlyChartData.labels},
            series: [
                {name: 'Receitas', data: monthlyChartData.income},
                {name: 'Despesas', data: monthlyChartData.expense}
            ]
        });
        monthlyChart.render();
    </script>
@endpush
