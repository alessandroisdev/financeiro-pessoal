@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="container" style="max-width: 420px;">
        <h1 class="mb-4 text-center">Entrar</h1>

        @include('partials.errors')

        @if(session('status'))
            <div class="alert alert-info">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input id="email" type="email" name="email"
                       class="form-control"
                       value="{{ old('email') }}"
                       required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input id="password" type="password" name="password"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label for="remember" class="form-check-label">Manter conectado</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right"></i> Entrar
            </button>

            <div class="mt-3 text-center">
                <a href="{{ route('register') }}">Criar uma conta</a>
            </div>
        </form>
    </div>
@endsection
