@extends('layouts.app')

@section('title', 'Editar Lançamento')

@section('content')
    <div class="container">
        <h1 class="mb-4">Editar Lançamento</h1>

        @include('partials.errors')

        <form action="{{ route('transactions.update', $transaction) }}" method="POST">
            @method('PUT')
            @include('transactions.form', ['transaction' => $transaction])
        </form>
    </div>
@endsection
