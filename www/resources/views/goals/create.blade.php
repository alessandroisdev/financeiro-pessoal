@extends('layouts.app')

@section('title', 'Nova Meta')

@section('content')
    <div class="container">
        <h1>Nova Meta</h1>
        @include('partials.errors')

        <form method="POST" action="{{ route('goals.store') }}">
            @include('goals.form')
        </form>
    </div>
@endsection
