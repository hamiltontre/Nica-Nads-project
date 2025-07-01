@extends('layouts.modern')

@section('title', 'Gestión de Atletas')

@section('content')
<link rel="stylesheet" href="{{ mix('css/atletas.css') }}">

<div class="container py-4 px-2">
    <!-- Tabs de navegación -->
    <ul class="nav nav-tabs mb-3 px-2" id="atletasTabs" role="tablist">
        @foreach($grupos as $grupo)
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                    id="{{ Str::slug($grupo) }}-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#{{ Str::slug($grupo) }}"
                    type="button"
                    role="tab"
                    data-grupo="{{ $grupo }}">
                {{ $grupo }}
            </button>
        </li>
        @endforeach
    </ul>

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('atletas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Atleta
        </a>
    </div>

    <div class="tab-content px-1" id="atletasTabsContent">
        @foreach($grupos as $grupo)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
             id="{{ Str::slug($grupo) }}"
             role="tabpanel">
            @if($loop->first)
                <div class="atletas-grid-container">
                    <div class="atletas-grid">
                        @include('atletas._atletas_list', [
                            'atletas' => $atletasPorGrupo[$grupo],
                            'diasHabiles' => $diasHabiles
                        ])
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Cargando atletas...</p>
                </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<script>
// Objeto para cache de grupos ya cargados
const gruposCargados = {};

document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#atletasTabs .nav-link');
    
    // Configurar evento para cada tab
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            const grupo = e.target.getAttribute('data-grupo');
            const target = e.target.getAttribute('data-bs-target');
            const tabContent = document.querySelector(target);
            
            // Solo cargar si no está en cache
            if (!gruposCargados[grupo]) {
                cargarAtletas(grupo);
            }
        });
    });
    
    // Inicializar funcionalidad de zoom para el primer grupo
    initImageZoom();
});

function cargarAtletas(grupo) {
    const tabContent = document.querySelector(`#${grupo.toLowerCase()}`);
    const url = "{{ route('atletas.byGrupo', ['grupo' => 'GRUPO_PLACEHOLDER']) }}".replace('GRUPO_PLACEHOLDER', grupo);
    
    // Mostrar loader
    tabContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Cargando atletas...</p>
        </div>
    `;
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            return response.json();
        })
        .then(data => {
            // Actualizar contenido
            tabContent.innerHTML = `
                <div class="atletas-grid-container">
                    <div class="atletas-grid">
                        ${data.html}
                    </div>
                </div>
            `;
            
            // Marcar grupo como cargado
            gruposCargados[grupo] = true;
            
            // Configurar eventos de paginación
            configurarPaginacion(grupo);
            
            // Inicializar zoom para las nuevas imágenes
            initImageZoom();
        })
        .catch(error => {
            console.error('Error:', error);
            tabContent.innerHTML = `
                <div class="alert alert-danger">
                    Error al cargar atletas: ${error.message}
                    <button onclick="cargarAtletas('${grupo}')" class="btn btn-sm btn-primary ms-2">Reintentar</button>
                </div>
            `;
        });
}

function configurarPaginacion(grupo) {
    document.querySelectorAll(`#${grupo.toLowerCase()} .pagination-custom a`).forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const pageUrl = new URL(this.href);
            const page = pageUrl.searchParams.get('page');
            cargarPaginaAtletas(grupo, page);
        });
    });
}

function cargarPaginaAtletas(grupo, page) {
    const tabContent = document.querySelector(`#${grupo.toLowerCase()}`);
    const url = "{{ route('atletas.byGrupo', ['grupo' => 'GRUPO_PLACEHOLDER']) }}".replace('GRUPO_PLACEHOLDER', grupo) + `?page=${page}`;
    
    // Mostrar loader solo en la grid
    const grid = tabContent.querySelector('.atletas-grid');
    if (grid) {
        grid.innerHTML = `
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Cargando página...</p>
            </div>
        `;
    }
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            return response.json();
        })
        .then(data => {
            // Reemplazar solo la parte de la lista de atletas
            const grid = tabContent.querySelector('.atletas-grid');
            if (grid) {
                grid.innerHTML = data.html;
                
                // Reconfigurar paginación
                configurarPaginacion(grupo);
                
                // Reinicializar zoom
                initImageZoom();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger';
            errorDiv.innerHTML = `
                Error al cargar página: ${error.message}
                <button onclick="cargarPaginaAtletas('${grupo}', ${page})" class="btn btn-sm btn-primary ms-2">Reintentar</button>
            `;
            
            const gridContainer = tabContent.querySelector('.atletas-grid-container');
            if (gridContainer) {
                gridContainer.parentNode.insertBefore(errorDiv, gridContainer.nextSibling);
            }
        });
}

function initImageZoom() {
    document.querySelectorAll('.atleta-card').forEach(card => {
        const foto = card.querySelector('.atleta-foto');
        if (!foto) return;
        
        // Crear overlay si no existe
        if (!card.querySelector('.imagen-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'imagen-overlay';
            const imgAmpliada = document.createElement('img');
            imgAmpliada.src = foto.src;
            overlay.appendChild(imgAmpliada);
            card.appendChild(overlay);

            // Eventos para mostrar/ocultar
            foto.addEventListener('click', function(e) {
                e.stopPropagation();
                overlay.classList.add('mostrar');
            });

            overlay.addEventListener('click', function(e) {
                e.stopPropagation();
                overlay.classList.remove('mostrar');
            });
        }
    });

    // Cerrar al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.imagen-overlay') && !e.target.closest('.atleta-foto')) {
            document.querySelectorAll('.imagen-overlay').forEach(overlay => {
                overlay.classList.remove('mostrar');
            });
        }
    });
}
</script>
@endsection