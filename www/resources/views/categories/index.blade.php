@extends('layouts.app')

@section('title', 'Categorias')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Categorias</h1>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nova Categoria
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="datatable-categories" class="table table-striped table-bordered w-100">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Tipo</th>
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

        const dt = new DataTableInit('#datatable-categories', {
            dom: 'Bfrtip',
            buttons: ['excel', 'csv', 'pdf', 'print'],
            ajax: '{{ route("categories.datatable") }}',
            columns: [
                {data: 'id'},
                {data: 'name'},
                {data: 'type'},
                {
                    data: 'active',
                    render: function (data) {
                        return data ? 'Sim' : 'Não';
                    }
                },
                {data: 'actions', orderable: false, searchable: false}
            ]
        });
    </script>
@endpush
