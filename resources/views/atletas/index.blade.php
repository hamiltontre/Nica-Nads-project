@extends('layouts.app')

@section('title', 'Gestión de Atletas')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Listado de Atletas</h2>
        <a href="{{ route('atletas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Atleta
        </a>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($atletas as $atleta)
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <!-- Foto del atleta -->
                        <div class="flex-shrink-0 me-3">
                            @if($atleta->foto)
                                <img src="{{ asset('storage/'.$atleta->foto) }}" 
                                     alt="Foto de {{ $atleta->nombre }}" 
                                     class="rounded-circle" 
                                     width="80" 
                                     height="80">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px;">
                                    <i class="bi bi-person" style="font-size: 2rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <!-- Nombre y edad -->
                            <h5 class="card-title mb-1">{{ $atleta->nombre }} {{ $atleta->apellido }}</h5>
                            <p class="text-muted mb-2">{{ $atleta->edad }} años</p>
                            
                            <!-- Grupo/Condición -->
                            @php
                                $badgeClass = [
                                    'Federados' => 'bg-primary',
                                    'Novatos' => 'bg-success',
                                    'Juniors' => 'bg-warning text-dark',
                                    'Principiantes' => 'bg-info text-dark'
                                ][$atleta->grupo] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }} mb-2">
                                {{ $atleta->grupo }}
                            </span>
                            
                            <!-- Estado de beca -->
                            <div class="mb-2">
                                @if($atleta->becado)
                                    <span class="badge bg-success">
                                        <i class="bi bi-award"></i> Becado
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        No becado
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="border-top pt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <h6 class="mb-1">Promedio</h6>
                                <span class="badge bg-light text-dark fs-6">--%</span>
                            </div>
                            <div class="col-4">
                                <h6 class="mb-1">Asistencias</h6>
                                <span class="badge bg-light text-dark fs-6">--</span>
                            </div>
                            <div class="col-4">
                                <h6 class="mb-1">Inasistencias</h6>
                                <span class="badge bg-light text-dark fs-6">--</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-end">
                        <a href="#" class="btn btn-sm btn-outline-primary me-2">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection