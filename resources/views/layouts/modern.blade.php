<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - NADS</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/logoWeb.png') }}" type="image/x-icon">
    
    <!-- Fuentes y estilos -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    @stack('styles')

    <style>
        /* Reset de paginación - debe ir antes de bootstrap */
        .pagination {
            --bs-pagination-padding-x: 0.5rem;
            --bs-pagination-padding-y: 0.25rem;
            --bs-pagination-font-size: 0.875rem;
            --bs-pagination-border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <img src="{{ asset('images/gradient.png') }}" alt="gradient" class="image-gradient">
    <div class="layer-blur"></div>

    <div class="main-container">
        <header>
            <div class="header-content">
                <h1 class="logo">Nica <span>Nads</span></h1>
                <nav class="navbar">
                    <ul class="nav-list">
                        <li><a href="{{ route('atletas.index') }}">Atletas</a></li>
                        <li><a href="{{ route('facturas.index') }}">Facturas</a></li>
                        <li><a href="{{ route('asistencias.index') }}">Asistencias</a></li>
                    </ul>
                    
                    @auth
                    <div class="profile-container">
                        <div class="profile-toggle">
                            <div class="profile-circle">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="profile-email">{{ auth()->user()->email }}</span>
                            <i class="bi bi-chevron-down profile-arrow"></i>
                        </div>
                        <div class="profile-dropdown">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="content-wrapper">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.profile-toggle').click(function(e) {
                e.stopPropagation();
                $(this).closest('.profile-container').find('.profile-dropdown').toggle();
                $(this).find('.profile-arrow').toggleClass('bi-chevron-down bi-chevron-up');
            });
            
            $(document).click(function() {
                $('.profile-dropdown').hide();
                $('.profile-arrow').removeClass('bi-chevron-up').addClass('bi-chevron-down');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
