<!DOCTYPE html>
<html lang="es">
<head>
 <meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Favicon para todos los dispositivos -->
    <link rel="shortcut icon" href="{{ asset('images/logoWeb.png') }}" type="image/x-icon"> 
    <!-- Para navegadores modernos -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logoWeb-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logoWeb-16x16.png') }}">
    
    <!-- Para Apple devices -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logoWeb-180x180.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') NADS</title>
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- CSS Personalizado -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <img src="{{ asset('images/gradient.png') }}" alt="gradient" class="image-gradient">
    <div class="layer-blur"></div>

    <div class="main-container">
        <header>
            <h1 class="logo">Nica Nads</h1>
            <nav class="navbar">
                <ul class="nav-list">
                    <li><a href="{{ route('atletas.index') }}">Atletas</a></li>
                    <li><a href="{{ route('facturas.index') }}">Facturas</a></li>
                    <li><a href="{{ route('asistencias.index') }}">Asistencias</a></li>
                </ul>
            </nav>
        </header>

        <main class="content-wrapper">
            @yield('content')
        </main>
    </div>

    <!-- jQuery primero (si lo necesitas) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('scripts') <!-- Aquí se inyectarán los scripts específicos de la vista -->
    <script src="{{ mix('js/asistencia.js') }}"></script>
</body>
</html>



