@extends('layouts.modern')

@section('title', 'Gestión de Atletas')

@section('content')
<div class="container py-4">
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="atletasTabs" role="tablist">
        @foreach($grupos as $grupo)
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                    id="{{ strtolower($grupo) }}-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#{{ strtolower($grupo) }}" 
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
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('atletas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Atleta
        </a>
    </div>

    <!-- Tabs Content -->
    <div class="tab-content" id="atletasTabsContent">
        @foreach($grupos as $grupo)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
             id="{{ strtolower($grupo) }}" 
             role="tabpanel">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
                @forelse($atletasPorGrupo[$grupo] as $atleta)
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
                                             width="70" 
                                             height="70">
                                    @else
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 70px; height: 70px;">
                                            <i class="bi bi-person" style="font-size: 1.8rem;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">{{ $atleta->nombre }} {{ $atleta->apellido }}</h5>
                                    <p class="text-muted mb-2">{{ $atleta->edad }} años</p>
                                    
                                    <span class="badge 
                                        @if($atleta->grupo == 'Federados') bg-primary
                                        @elseif($atleta->grupo == 'Novatos') bg-success
                                        @elseif($atleta->grupo == 'Juniors') bg-warning text-dark
                                        @else bg-info text-dark
                                        @endif mb-2">
                                        {{ $atleta->grupo }}
                                    </span>
                                    
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
                                <a href="{{ route('atletas.edit', $atleta->id) }}" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('atletas.destroy', $atleta->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este atleta?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-people-fill fs-1"></i>
                        <h4 class="mt-3">No hay atletas en este grupo</h4>
                    </div>
                </div>
                @endforelse
            </div>

            @if($atletasPorGrupo[$grupo]->hasPages())
            <div class="mt-4">
                {{ $atletasPorGrupo[$grupo]->appends(['page_' . $grupo => request()->input('page_' . $grupo)])->onEachSide(1)->links() }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection
