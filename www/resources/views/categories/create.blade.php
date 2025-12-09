@extends('layouts.app')

@section('title', 'Nova Categoria')

@section('content')
    <div class="container">
        <h1 class="mb-4">Nova Categoria</h1>

        @include('partials.errors')

        <form action="{{ route('categories.store') }}" method="POST">
            @include('categories.form')
        </form>
    </div>
@endsection
