@forelse($atletas as $atleta)
@php
    $totalPresente = $atleta->asistencias->where('estado', 'presente')->count();
    $totalAusente = $atleta->asistencias->where('estado', 'ausente')->count();
    $totalJustificado = $atleta->asistencias->where('estado', 'justificado')->count();
    $totalClases = $atleta->grupo == 'Federados' ? $diasHabiles * 2 : $diasHabiles;
@endphp

<div class="atleta-card-container">
    <div class="card h-100 shadow-sm atleta-card">
        <div class="card-body">
            <div class="d-flex align-items-start">
                <div class="img-container me-3">
                    @if($atleta->foto)
                        <img src="{{ asset('storage/'.$atleta->foto) }}" 
                             class="atleta-foto"
                             alt="Foto de {{ $atleta->nombre }}">
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
                        {{ $atleta->fecha_nacimiento->format('d/m/Y') }}
                        ({{ $atleta->edad }} años)
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
                    <div class="col-4 stat-item">
                        <h6 class="mb-1">Asistencia</h6>
                        <span class="stat-value text-success">{{ $totalPresente }}</span>
                    </div>
                    <div class="col-4 stat-item">
                        <h6 class="mb-1">Inasis.</h6>
                        <span class="stat-value text-danger">{{ $totalAusente }}</span>
                    </div>
                    <div class="col-4 stat-item">
                        <h6 class="mb-1">Justif.</h6>
                        <span class="stat-value text-warning">{{ $totalJustificado }}</span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 text-center">
                        <small class="text-muted">
                            {{ $totalPresente + $totalAusente + $totalJustificado }}/{{ $totalClases }} clases
                        </small>
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
@if($atletas->hasPages())
<div class="mt-4">
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center pagination-custom">
            {{-- Previous Page Link --}}
            @if ($atletas->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $atletas->previousPageUrl() }}" rel="prev">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($atletas->getUrlRange(1, $atletas->lastPage()) as $page => $url)
                @if ($page == $atletas->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($atletas->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $atletas->nextPageUrl() }}" rel="next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
</div>
@endif