@extends('layouts.app')

@section('title', 'Nova Conta')

@section('content')
    <div class="container">
        <h1 class="mb-4">Nova Conta</h1>

        @include('partials.errors')

        <form action="{{ route('accounts.store') }}" method="POST">
            @include('accounts.form')
        </form>
    </div>
@endsection
