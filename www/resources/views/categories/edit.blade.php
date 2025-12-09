@extends('layouts.app')

@section('title', 'Editar Categoria')

@section('content')
    <div class="container">
        <h1 class="mb-4">Editar Categoria</h1>

        @include('partials.errors')

        <form action="{{ route('categories.update', $category) }}" method="POST">
            @method('PUT')
            @include('categories.form', ['category' => $category])
        </form>
    </div>
@endsection
