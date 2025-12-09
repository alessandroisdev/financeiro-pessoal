@extends('layouts.app')

@section('title', 'Editar Lançamento')

@section('content')
    <div class="container">
        <h1 class="mb-4">Editar Lançamento</h1>

        @include('partials.errors')

        <form action="{{ route('transactions.update', $transaction) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @include('transactions.form', ['transaction' => $transaction])
        </form>

        @if(isset($transaction) && $transaction->attachments->count())
            <hr>
            <h3>Anexos</h3>
            <ul class="list-group mb-3">
                @foreach($transaction->attachments as $att)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-paperclip"></i>
                            <a href="{{ route('attachments.download', $att) }}">
                                {{ $att->original_name }}
                            </a>
                            <small class="text-muted">
                                ({{ number_format($att->size / 1024, 1, ',', '.') }} KB)
                            </small>
                        </div>
                        <form action="{{ route('attachments.destroy', $att) }}" method="POST"
                              onsubmit="return confirm('Remover este anexo?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif

    </div>
@endsection
