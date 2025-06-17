@extends('layouts.modern')

@section('title', 'Registro de Asistencias')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header text-white d-flex justify-content-between align-items-center 
            {{ $turno == 'mañana' ? 'bg-primary' : 'bg-warning' }}">
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" id="grupoDropdown" data-bs-toggle="dropdown">
                    <i class="bi bi-people-fill"></i> Grupo: {{ $grupoSeleccionado }}
                </button>
                <ul class="dropdown-menu">
                    @foreach($gruposValidos as $grupo)
                    <li>
                        <a class="dropdown-item {{ $grupoSeleccionado == $grupo ? 'active' : '' }}" 
                           href="{{ route('asistencias.index', ['grupo' => $grupo]) }}">
                           @if($grupo == 'Federados') <i class="bi bi-award-fill"></i>
                           @elseif($grupo == 'Novatos') <i class="bi bi-star-fill"></i>
                           @elseif($grupo == 'Juniors') <i class="bi bi-emoji-smile-fill"></i>
                           @else <i class="bi bi-person-fill-add"></i>
                           @endif
                           {{ $grupo }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="d-flex align-items-center">
                @if($grupoSeleccionado === 'Federados')
                <select id="turnoSelect" class="form-select me-2" style="width: 140px;">
                    <option value="mañana" {{ $turno == 'mañana' ? 'selected' : '' }}>Turno Mañana</option>
                    <option value="tarde" {{ $turno == 'tarde' ? 'selected' : '' }}>Turno Tarde</option>
                </select>
                @endif
                
                <div class="input-group" style="width: 200px;">
                    <select id="mesSelect" class="form-select">
                        @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ $month == $mesActual ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                        </option>
                        @endforeach
                    </select>
                    <select id="anioSelect" class="form-select">
                        @foreach(range(date('Y'), date('Y') - 2) as $year)
                        <option value="{{ $year }}" {{ $year == $anioActual ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="d-flex justify-content-between mb-4">
                <h5>
                    <i class="bi bi-calendar-check"></i> 
                    {{ DateTime::createFromFormat('!m', $mesActual)->format('F') }} {{ $anioActual }}
                    @if($grupoSeleccionado === 'Federados')
                    - Turno {{ ucfirst($turno) }}
                    @endif
                </h5>
                
                <div>
                    <button id="iniciarBtn" class="btn btn-primary me-2">
                        <i class="bi bi-pencil"></i> Iniciar Asistencia
                    </button>
                    <button id="guardarBtn" class="btn btn-success" disabled>
                        <i class="bi bi-save"></i> Guardar Asistencia
                    </button>
                </div>
            </div>

            @if($atletas->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No hay atletas registrados en este grupo.
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="position: sticky; left: 0; background: white; z-index: 10;">Atleta</th>
                            @foreach($diasMes as $dia)
                            <th class="text-center {{ $dia['es_domingo'] ? 'bg-light' : '' }}" style="min-width: 50px;">
                                <div class="d-flex flex-column">
                                    <small>{{ date('D', strtotime($dia['fecha'])) }}</small>
                                    <strong>{{ $dia['dia'] }}</strong>
                                    @if($dia['es_domingo'])
                                    <small class="text-muted">Libre</small>
                                    @endif
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($atletas as $atleta)
                        <tr>
                            <td style="position: sticky; left: 0; background: white; z-index: 10;">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        @if($atleta->foto && Storage::disk('public')->exists($atleta->foto))
                                            <img src="{{ asset('storage/'.$atleta->foto) }}" 
                                                 class="rounded-circle" 
                                                 width="32" height="32"
                                                 style="object-fit: cover;">
                                        @else
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 32px; height: 32px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $atleta->nombre }}</div>
                                        <small class="text-muted">{{ $atleta->apellido }}</small>
                                    </div>
                                </div>
                            </td>
                            
                            @foreach($diasMes as $dia)
                            <td class="text-center align-middle {{ $dia['es_domingo'] ? 'bg-light' : '' }} asistencia-td"
                                data-atleta-id="{{ $atleta->id }}"
                                data-fecha="{{ $dia['fecha'] }}"
                                data-turno="{{ $turno }}"
                                @if(isset($asistencias[$atleta->id][$dia['fecha']][0]))
                                    data-estado="{{ $asistencias[$atleta->id][$dia['fecha']][0]->estado }}"
                                @else
                                    data-estado="libre"
                                @endif
                                >
                                @if($dia['es_domingo'])
                                <span class="text-muted">-</span>
                                @else
                                <div class="asistencia-cell">
                                    @if(isset($asistencias[$atleta->id][$dia['fecha']][0]))
                                        @if($asistencias[$atleta->id][$dia['fecha']][0]->estado == 'presente')
                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                        @elseif($asistencias[$atleta->id][$dia['fecha']][0]->estado == 'ausente')
                                        <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                        @else
                                        <i class="bi bi-circle text-muted fs-5"></i>
                                        @endif
                                    @else
                                        <i class="bi bi-circle text-muted fs-5"></i>
                                    @endif
                                </div>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.asistenciaConfig = {
    storeUrl: '{{ route("asistencias.store") }}',
    csrfToken: '{{ csrf_token() }}'
};
</script>
<script src="{{ asset('js/asistencia.js') }}"></script>
@endpush

@push('styles')
<link href="{{ asset('css/asistencia.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
@endpush