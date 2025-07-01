@extends('layouts.modern')

@section('title', 'Registro de Asistencias')

@section('content')
<div class="container-fluid py-4 px-3">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-calendar-check me-2"></i>
                    Asistencias - {{ now()->translatedFormat('F Y') }}
                </h4>
                
                <!-- Selector de grupo y turno -->
                <div class="d-flex">
                    <div class="dropdown me-2">
                        <button class="btn btn-light dropdown-toggle" type="button" id="grupoDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $grupoSeleccionado }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="grupoDropdown">
                            @foreach($grupos as $grupo)
                            <li>
                                <a class="dropdown-item {{ $grupoSeleccionado == $grupo ? 'active' : '' }}" 
                                   href="{{ route('asistencias.index', ['grupo' => $grupo]) }}">
                                    {{ $grupo }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    @if($grupoSeleccionado === 'Federados')
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" id="turnoDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            {{ ucfirst($turno) }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="turnoDropdown">
                            <li>
                                <a class="dropdown-item {{ $turno === 'mañana' ? 'active' : '' }}" 
                                   href="{{ route('asistencias.index', ['grupo' => $grupoSeleccionado, 'turno' => 'mañana']) }}">
                                    Mañana
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $turno === 'tarde' ? 'active' : '' }}" 
                                   href="{{ route('asistencias.index', ['grupo' => $grupoSeleccionado, 'turno' => 'tarde']) }}">
                                    Tarde
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <button id="iniciarBtn" class="btn btn-primary me-2">
                        <i class="bi bi-pencil"></i> Iniciar Asistencia
                    </button>
                    <button id="guardarBtn" class="btn btn-success" disabled>
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
                <div>
                    <span class="badge bg-success me-2">
                        Total días: {{ count(array_filter($diasMes, fn($dia) => !$dia['es_domingo'])) }}
                    </span>
                    <a href="{{ route('asistencias.historico') }}" class="btn btn-info">
                        <i class="bi bi-clock-history"></i> Ver Histórico
                    </a>
                </div>
            </div>
            
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-bordered table-hover">
                    <colgroup>
                        <col style="width: 300px; position: sticky; left: 0; background: white; z-index: 10;">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th style="position: sticky; left: 0; z-index: 20;">Atleta</th>
                            @foreach($diasMes as $dia)
                            <th class="text-center {{ $dia['es_domingo'] ? 'bg-secondary' : '' }}" 
                                title="{{ $dia['dia_semana'] }} {{ $dia['dia'] }}">
                                {{ $dia['dia_semana'] }}
                                <div class="small">{{ $dia['dia'] }}</div>
                            </th>
                            @endforeach
                            <th class="text-center bg-primary text-white" style="position: sticky; right: 0;">
                                Asistencia
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atletas as $atleta)
                        @php
                            $totalPresente = 0;
                            $totalAusente = 0;
                            $totalDias = 0;
                            
                            foreach($diasMes as $dia) {
                                if(!$dia['es_domingo']) {
                                    $totalDias++;
                                    $asistenciaManana = $asistencias[$atleta->id][$dia['fecha']]['mañana'][0] ?? null;
                                    $asistenciaTarde = $asistencias[$atleta->id][$dia['fecha']]['tarde'][0] ?? null;
                                    
                                    if($asistenciaManana && $asistenciaManana->estado == 'presente') $totalPresente++;
                                    if($asistenciaTarde && $asistenciaTarde->estado == 'presente') $totalPresente++;
                                    if($asistenciaManana && $asistenciaManana->estado == 'ausente') $totalAusente++;
                                    if($asistenciaTarde && $asistenciaTarde->estado == 'ausente') $totalAusente++;
                                }
                            }
                        @endphp
                        <tr>
                            <td style="position: sticky; left: 0; background: white; z-index: 10;">
                                <div class="d-flex align-items-center">
                                    @if($atleta->foto)
                                    <img src="{{ asset('storage/'.$atleta->foto) }}" 
                                         alt="{{ $atleta->nombre }}" 
                                         class="rounded-circle me-2" 
                                         width="40" height="40">
                                    @else
                                    <div class="rounded-circle bg-light text-center me-2" 
                                         style="width:40px;height:40px;line-height:40px;">
                                        <i class="bi bi-person text-muted"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <!-- MODIFICACIÓN: Añadida clase atleta-nombre -->
                                        <strong class="atleta-nombre">{{ $atleta->nombre }} {{ $atleta->apellido }}</strong>
                                        
                                    </div>
                                </div>
                            </td>
                            
                            @foreach($diasMes as $dia)
                            @php
                                $asistencia = $asistencias[$atleta->id][$dia['fecha']][$turno][0] ?? null;
                                $estado = $asistencia->estado ?? 'libre';
                                $esDomingo = $dia['es_domingo'];
                            @endphp
                            <td class="text-center align-middle asistencia-td 
                                {{ $esDomingo ? 'bg-light' : '' }}"
                                data-atleta-id="{{ $atleta->id }}"
                                data-fecha="{{ $dia['fecha'] }}"
                                data-turno="{{ $turno }}"
                                data-estado="{{ $estado }}"
                                title="{{ $dia['dia_semana'] }} {{ $dia['dia'] }}">
                                @if(!$esDomingo)
                                <div class="asistencia-cell">
                                    <!-- MODIFICACIÓN: Añadidos radios de asistencia -->
                                    <div class="form-check d-flex justify-content-center gap-2">
                                        <input class="form-check-input presente-radio" type="radio" 
                                               name="asistencia_{{ $atleta->id }}_{{ $dia['fecha'] }}" 
                                               value="presente" {{ $estado == 'presente' ? 'checked' : '' }}>
                                        <input class="form-check-input ausente-radio" type="radio" 
                                               name="asistencia_{{ $atleta->id }}_{{ $dia['fecha'] }}" 
                                               value="ausente" {{ $estado == 'ausente' ? 'checked' : '' }}>
                                        <input class="form-check-input libre-radio" type="radio" 
                                               name="asistencia_{{ $atleta->id }}_{{ $dia['fecha'] }}" 
                                               value="libre" {{ $estado != 'presente' && $estado != 'ausente' ? 'checked' : '' }}>
                                    </div>
                                    
                                    <!-- Iconos que se muestran cuando no está en modo edición -->
                                    @if($estado == 'presente')
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    @elseif($estado == 'ausente')
                                    <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                    @else
                                    <i class="bi bi-circle text-muted fs-5"></i>
                                    @endif
                                </div>
                                @endif
                            </td>
                            @endforeach
                            
                            <td class="text-center align-middle bg-light" 
                                style="position: sticky; right: 0; background: #f8f9fa!important;">
                                <span class="badge bg-success">{{ $totalPresente }}</span>
                                <span class="badge bg-danger">{{ $totalAusente }}</span>
                                <div class="small mt-1">
                                    {{ round(($totalPresente / ($totalDias * ($atleta->grupo == 'Federados' ? 2 : 1))) * 100) }}%
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.asistenciaConfig = {
    storeUrl: "{{ route('asistencias.store') }}",
    csrfToken: "{{ csrf_token() }}"
};
</script>
<script src="{{ asset('js/asistencia.js') }}"></script>
@endsection