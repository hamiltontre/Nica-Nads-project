@extends('layouts.modern')

@section('title', 'Gestión de Atletas')

@section('content')
<link rel="stylesheet" href="{{ mix('css/atletas.css') }}">

@php
    $totalAtletas = 0;
    foreach ($atletasPorGrupo as $grupoAtletas) {
        $totalAtletas += $grupoAtletas->total();
    }
@endphp

<div class="container py-4 px-2">
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-3 px-2" id="atletasTabs" role="tablist">
        @foreach($grupos as $grupo)
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                    id="{{ Str::slug($grupo) }}-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#{{ Str::slug($grupo) }}"
                    type="button"
                    role="tab">
                <i class="bi 
                    @if($grupo == 'Federados') bi-award-fill
                    @elseif($grupo == 'Novatos') bi-star-fill
                    @elseif($grupo == 'Juniors') bi-emoji-smile-fill
                    @else bi-person-fill-add
                    @endif
                    me-1"></i>
                {{ $grupo }}
            </button>
        </li>
        @endforeach
    </ul>

    <!-- Botón para agregar nuevo atleta -->
    <div class="d-flex justify-content-between mb-4 px-2">
        
        <a href="{{ route('atletas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Atleta
        </a>
    </div>

    <!-- Tabs Content -->
    <div class="tab-content px-1" id="atletasTabsContent">
        @foreach($grupos as $grupo)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
             id="{{ Str::slug($grupo) }}"
             role="tabpanel">
             
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                @forelse($atletasPorGrupo[$grupo] as $atleta)
                <div class="col">
                    <div class="card h-100 shadow-sm atleta-card">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="img-hover-container me-3">
                                    @if($atleta->foto)
                                        <img src="{{ asset('storage/'.$atleta->foto) }}"
                                             alt="Foto de {{ $atleta->nombre }}"
                                             class="atleta-foto"
                                             title="{{ $atleta->nombre }} {{ $atleta->apellido }}">
                                    @else
                                        <div class="foto-placeholder">
                                            <i class="bi bi-person"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">{{ $atleta->nombre }} {{ $atleta->apellido }}</h5>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ date('d/m/Y', strtotime($atleta->fecha_nacimiento)) }}
                                        ({{ \Carbon\Carbon::parse($atleta->fecha_nacimiento)->age }} años)
                                    </p>
                                    
                                    <span class="badge badge-grupo 
                                        @if($atleta->grupo == 'Federados') badge-federados
                                        @elseif($atleta->grupo == 'Novatos') badge-novatos
                                        @elseif($atleta->grupo == 'Juniors') badge-juniors
                                        @else badge-otros
                                        @endif mb-2">
                                        {{ $atleta->grupo }}
                                    </span>

                                    @if($atleta->becado)
                                        <span class="badge bg-success bg-opacity-10 text-success small mb-2">
                                            <i class="bi bi-award me-1"></i> Becado
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="stats-container mt-3">
    <div class="row text-center">
        <div class="col-6 stat-item">
            <h6 class="mb-1">Asistencia</h6>
            <span class="stat-value">
                {{ $atleta->asistencias->where('estado', 'presente')->count() }}/{{ $atleta->asistencias->count() }}
            </span>
        </div>
        <div class="col-6 stat-item">
            <h6 class="mb-1">Inasis.</h6>
            <span class="stat-value text-danger">
                {{ $atleta->asistencias->where('estado', 'ausente')->count() }}
            </span>
        </div>
    </div>
</div>

                        </div>
                        <div class="card-footer bg-transparent py-2">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('atletas.edit', $atleta->id) }}" 
                                   class="btn btn-sm btn-outline-primary me-2"
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('atletas.destroy', $atleta->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('¿Estás seguro de eliminar este atleta?')"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info text-center py-4">
                        <i class="bi bi-people-fill fs-1 text-primary"></i>
                        <h4 class="mt-3">No hay atletas en este grupo</h4>
                        <p class="mb-0">Puedes agregar nuevos atletas haciendo clic en el botón "Nuevo Atleta"</p>
                    </div>
                </div>
                @endforelse
            </div>

            @if($atletasPorGrupo[$grupo]->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $atletasPorGrupo[$grupo]->appends(['page_' . $grupo => request()->input('page_' . $grupo)])->onEachSide(1)->links() }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.atleta-card');
    
    cards.forEach(card => {
        const foto = card.querySelector('.atleta-foto');
        if (!foto) return;
        
        const overlay = document.createElement('div');
        overlay.className = 'imagen-overlay';
        const imgAmpliada = document.createElement('img');
        imgAmpliada.src = foto.src;
        overlay.appendChild(imgAmpliada);
        card.appendChild(overlay);

        foto.addEventListener('click', function(e) {
            e.stopPropagation();
            overlay.classList.add('mostrar');
        });

        overlay.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.remove('mostrar');
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.imagen-overlay') && !e.target.closest('.atleta-foto')) {
            document.querySelectorAll('.imagen-overlay').forEach(overlay => {
                overlay.classList.remove('mostrar');
            });
        }
    });
});
</script>
@endsection
