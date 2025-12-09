@extends('layouts.app')

@section('title', 'Lançamentos')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Lançamentos</h1>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Novo Lançamento
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @php
            $accounts = \App\Models\Account::where('user_id', auth()->id())->orderBy('name')->get();
            $categories = \App\Models\Category::where('user_id', auth()->id())->orderBy('name')->get();
        @endphp

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="bi bi-funnel"></i> Filtros</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="filter-date-start" class="form-label">Data Inicial</label>
                        <input type="date" id="filter-date-start" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="filter-date-end" class="form-label">Data Final</label>
                        <input type="date" id="filter-date-end" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="filter-account" class="form-label">Conta</label>
                        <select id="filter-account" class="form-control">
                            <option value="">Todas</option>
                            @foreach($accounts as $a)
                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-category" class="form-label">Categoria</label>
                        <select id="filter-category" class="form-control">
                            <option value="">Todas</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-type" class="form-label mt-3 mt-md-0">Tipo</label>
                        <select id="filter-type" class="form-control">
                            <option value="">Todos</option>
                            <option value="income">Receita</option>
                            <option value="expense">Despesa</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button id="btn-filter-clear" class="btn btn-outline-secondary w-100">
                            Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <table id="datatable-transactions" class="table table-striped table-bordered w-100">
            <thead>
            <tr>
                <th>ID</th>
                <th>Conta</th>
                <th>Categoria</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Data</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script type="module">
        import DataTableInit from '/js/datatable.js';

        const dt = new DataTableInit('#datatable-transactions', {
            dom: 'Bfrtip',
            buttons: ['excel', 'csv', 'pdf', 'print'],
            ajax: {
                url: '{{ route("transactions.datatable") }}',
                data: function (d) {
                    d.date_start = $('#filter-date-start').val();
                    d.date_end = $('#filter-date-end').val();
                    d.account_id = $('#filter-account').val();
                    d.category_id = $('#filter-category').val();
                    d.type = $('#filter-type').val();
                }
            },
            columns: [
                {data: 'id'},
                {data: 'account_name'},
                {data: 'category_name'},
                {
                    data: 'type',
                    render: function (data) {
                        return data === 'income' ? 'Receita' : 'Despesa';
                    }
                },
                {data: 'formatted_amount'},
                {data: 'formatted_date'},
                {
                    data: 'status',
                    render: function (data) {
                        switch (data) {
                            case 'pending':
                                return 'Pendente';
                            case 'paid':
                                return 'Pago';
                            case 'canceled':
                                return 'Cancelado';
                            default:
                                return data;
                        }
                    }
                },
                {data: 'actions', orderable: false, searchable: false}
            ]
        });

        $('#filter-date-start, #filter-date-end, #filter-account, #filter-category, #filter-type')
            .on('change', function () {
                dt.reload();
            });

        $('#btn-filter-clear').on('click', function (e) {
            e.preventDefault();
            $('#filter-date-start').val('');
            $('#filter-date-end').val('');
            $('#filter-account').val('');
            $('#filter-category').val('');
            $('#filter-type').val('');
            dt.reload();
        });
    </script>
@endpush
