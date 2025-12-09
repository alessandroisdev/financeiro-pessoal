@extends('layouts.app')

@section('title','Relatório de Lançamentos')

@section('content')
    <div class="container">
        <h1>Relatório de Lançamentos</h1>

        <form method="GET" action="{{ route('reports.transactions.excel') }}" class="mb-3" id="form-filters">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Data Inicial</label>
                    <input type="date" name="date_start" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data Final</label>
                    <input type="date" name="date_end" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Conta</label>
                    <select name="account_id" class="form-control">
                        <option value="">Todas</option>
                        @foreach($accounts as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoria</label>
                    <select name="category_id" class="form-control">
                        <option value="">Todas</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-control">
                        <option value="">Todos</option>
                        <option value="income">Receita</option>
                        <option value="expense">Despesa</option>
                    </select>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button formaction="{{ route('reports.transactions.excel') }}" class="btn btn-success">
                    Exportar Excel
                </button>
                <button formaction="{{ route('reports.transactions.pdf') }}" class="btn btn-danger">
                    Exportar PDF
                </button>
            </div>
        </form>
    </div>
@endsection
