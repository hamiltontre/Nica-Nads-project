<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NADS Web - @yield('title')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Estilos personalizados */
        body {
            padding-top: 56px;
        }
        .navbar {
            background-color: #0d6efd;
        }
        .active-nav {
            font-weight: bold;
            background-color: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('atletas.index') }}">NADS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('atletas.*') ? 'active-nav' : '' }}" 
                           href="{{ route('atletas.create') }}">Atletas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('facturas.*') ? 'active-nav' : '' }}" 
                           href="{{ route('facturas.index') }}">Facturas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('asistencias.*') ? 'active-nav' : '' }}" 
                           href="{{ route('asistencias.index') }}">Asistencias</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido -->
    <main class="container py-4">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>