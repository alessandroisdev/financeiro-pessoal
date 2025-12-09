@extends('layouts.app')

@section('title', 'Editar Conta')

@section('content')
    <div class="container">
        <h1 class="mb-4">Editar Conta</h1>

        @include('partials.errors')

        <form action="{{ route('accounts.update', $account) }}" method="POST">
            @method('PUT')
            @include('accounts.form', ['account' => $account])
        </form>
    </div>
@endsection
