@extends('layouts.app')

@section('title', 'Novo Lançamento')

@section('content')
    <div class="container">
        <h1 class="mb-4">Novo Lançamento</h1>

        @include('partials.errors')

        <form action="{{ route('transactions.store') }}" method="POST">
            @include('transactions.form')
        </form>
    </div>
@endsection
