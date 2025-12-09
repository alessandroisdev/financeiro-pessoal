@extends('layouts.app')

@section('title', 'Minhas Contas')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Minhas Contas</h1>
            <a href="{{ route('accounts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nova Conta
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="datatable-accounts" class="table table-striped table-bordered w-100">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Saldo Atual</th>
                <th>Ativo</th>
                <th>Ações</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script type="module">
        import DataTableInit from '/js/datatable.js';

        const dt = new DataTableInit('#datatable-accounts', {
            dom: 'Bfrtip',
            buttons: ['excel', 'csv', 'pdf', 'print'],
            ajax: '{{ route("accounts.datatable") }}',
            columns: [
                {data: 'id'},
                {data: 'name'},
                {data: 'type_label'},
                {data: 'formatted_balance'},
                {data: 'active_label'},
                {data: 'actions', orderable: false, searchable: false}
            ]
        });
    </script>
@endpush
