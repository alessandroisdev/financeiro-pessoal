@extends('layouts.app')

@section('title', 'Editar Meta')

@section('content')
    <div class="container">
        <h1>Editar Meta</h1>
        @include('partials.errors')

        <form method="POST" action="{{ route('goals.update', $goal) }}">
            @method('PUT')
            @include('goals.form', ['goal' => $goal])
        </form>
    </div>
@endsection
