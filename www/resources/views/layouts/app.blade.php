<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title', config('app.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    @stack('styles')

    <style>
        .nav-link.active {
            font-weight: bold;
            color: #fff !important;
        }

        .nav-link:hover {
            color: #e0e0e0 !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">

        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            <i class="bi bi-wallet2"></i> {{ config('app.name') }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMain" aria-controls="navbarMain"
                aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">

            @auth
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}"
                           href="{{ route('accounts.index') }}">
                            <i class="bi bi-bank2"></i> Contas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                           href="{{ route('categories.index') }}">
                            <i class="bi bi-tag-fill"></i> Categorias
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
                           href="{{ route('transactions.index') }}">
                            <i class="bi bi-list-columns-reverse"></i> Lançamentos
                        </a>
                    </li>

                </ul>

                <div class="d-flex align-items-center">
                    <span class="text-light me-3">
                        <i class="bi bi-person-circle"></i>
                        {{ auth()->user()->name }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </button>
                    </form>
                </div>
            @endauth

            @guest
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">Entrar</a>
                    </li>
                </ul>
            @endguest

        </div>
    </div>
</nav>

<main class="pb-5">
    @yield('content')
</main>

{{-- jQuery + Bootstrap JS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- DataTables + Buttons --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

@stack('scripts')
</body>
</html>
