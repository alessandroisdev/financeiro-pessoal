@extends('layouts.app')

@section('title', 'Criar conta')

@section('content')
    <div class="container" style="max-width: 420px;">
        <h1 class="mb-4 text-center">Criar conta</h1>

        @include('partials.errors')

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input id="name" type="text" name="name"
                       class="form-control"
                       value="{{ old('name') }}"
                       required autofocus>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input id="email" type="email" name="email"
                       class="form-control"
                       value="{{ old('email') }}"
                       required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input id="password" type="password" name="password"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar senha</label>
                <input id="password_confirmation" type="password"
                       name="password_confirmation"
                       class="form-control"
                       required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-person-plus"></i> Registrar
            </button>

            <div class="mt-3 text-center">
                <a href="{{ route('login') }}">Já tenho conta</a>
            </div>
        </form>
    </div>
@endsection
