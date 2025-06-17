<!DOCTYPE html>
<html lang="es">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .navbar-logo {
            height: 70px; /* Tamaño aumentado */
            width: auto;
            margin-right: 5px; /* Más espacio a la derecha */
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <div class="d-flex align-items-center">
                <!-- Logo más grande y con más margen derecho -->
                <img class="navbar-logo" src="{{ asset('images/LogoNads.png') }}" alt="Logo de Nads">
                <!--<a class="navbar-brand" href="{{ route('atletas.index') }}">NADS</a>-->
            </div>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('atletas.*') ? 'active-nav' : '' }}" 
                           href="{{ route('atletas.index') }}">
                           <i class="bi bi-people-fill me-1"></i>Atletas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('facturas.*') ? 'active-nav' : '' }}" 
                           href="{{ route('facturas.index') }}">
                           <i class="bi bi-receipt me-1"></i>Facturas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('asistencias.*') ? 'active-nav' : '' }}" 
                           href="{{ route('asistencias.index') }}">
                           <i class="bi bi-calendar-check me-1"></i>Asistencias
                        </a>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>