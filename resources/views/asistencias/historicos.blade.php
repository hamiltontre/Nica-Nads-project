@extends('layouts.modern')

@section('title', 'Histórico de Asistencias')

@section('content')
<div class="container-fluid py-4 px-3">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Histórico de Asistencias - {{ $meses[$mes] }} {{ $anio }}
                </h4>
                
                <!-- Filtros -->
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <form method="GET" class="d-flex flex-wrap gap-2">
                        <!-- Dropdown para seleccionar grupo -->
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" 
                                    id="historicoGrupoDropdown" data-bs-toggle="dropdown">
                                {{ $grupoSeleccionado }}
                            </button>
                            <ul class="dropdown-menu">
                                @foreach($grupos as $grupo)
                                <li>
                                    <a class="dropdown-item {{ $grupoSeleccionado == $grupo ? 'active' : '' }}" 
                                       href="{{ route('asistencias.historico', [
                                           'grupo' => $grupo,
                                           'turno' => $turno,
                                           'mes' => $mes,
                                           'anio' => $anio
                                       ]) }}">
                                        {{ $grupo }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <!-- Dropdown para seleccionar turno (solo para Federados) -->
                        @if($grupoSeleccionado === 'Federados')
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" 
                                    id="historicoTurnoDropdown" data-bs-toggle="dropdown">
                                {{ ucfirst($turno) }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item {{ $turno === 'mañana' ? 'active' : '' }}" 
                                       href="{{ route('asistencias.historico', [
                                           'grupo' => $grupoSeleccionado,
                                           'turno' => 'mañana',
                                           'mes' => $mes,
                                           'anio' => $anio
                                       ]) }}">
                                        Mañana
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ $turno === 'tarde' ? 'active' : '' }}" 
                                       href="{{ route('asistencias.historico', [
                                           'grupo' => $grupoSeleccionado,
                                           'turno' => 'tarde',
                                           'mes' => $mes,
                                           'anio' => $anio
                                       ]) }}">
                                        Tarde
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @endif
                        
                        <!-- Selector de mes -->
                        <select name="mes" class="form-select" onchange="this.form.submit()">
                            @foreach($meses as $key => $nombre)
                            <option value="{{ $key }}" {{ $mes == $key ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                            @endforeach
                        </select>
                        
                        <!-- Selector de año -->
                        <select name="anio" class="form-select" onchange="this.form.submit()">
                            @foreach($anios as $anioOption)
                            <option value="{{ $anioOption }}" {{ $anio == $anioOption ? 'selected' : '' }}>
                                {{ $anioOption }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Tabla de asistencias históricas -->
       <div class="table-responsive" style="overflow-x: auto; overflow-y: auto; max-height: 70vh;">
    <table class="table table-bordered table-hover">
                    <colgroup>
                        <col style="width: 300px; min-width: 200px;">
                    </colgroup>
                    <thead class="table-dark">
                        <tr>
                            <th style="position: sticky; left: 0; z-index: 10;">Atleta</th>
                            @foreach($diasMes as $dia)
                            <th class="text-center {{ $dia['es_domingo'] || $dia['es_dia_inhabil'] ? 'bg-secondary' : '' }}" 
                                title="{{ $dia['dia_semana'] }} {{ $dia['dia'] }}">
                                <span class="d-block d-md-none">{{ substr($dia['dia_semana'], 0, 1) }}</span>
                                <span class="d-none d-md-block">{{ $dia['dia_semana'] }}</span>
                                <div class="small">{{ $dia['dia'] }}</div>
                            </th>
                            @endforeach
                            <th class="text-center bg-primary text-white" style="position: sticky; right: 0;">
                                Totales
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atletas as $atleta)
                        @php
                            // Cálculo de totales para cada atleta
                            $totalPresente = 0;
                            $totalAusente = 0;
                            $totalJustificado = 0;
                            $totalDias = 0;
                            
                            foreach($diasMes as $dia) {
                                if(!$dia['es_domingo'] && !$dia['es_dia_inhabil']) {
                                    $totalDias++;
                                    $asistenciaManana = $asistencias[$atleta->id][$dia['fecha']]['mañana'][0] ?? null;
                                    $asistenciaTarde = $asistencias[$atleta->id][$dia['fecha']]['tarde'][0] ?? null;
                                    
                                    if($asistenciaManana && $asistenciaManana->estado == 'presente') $totalPresente++;
                                    if($asistenciaTarde && $asistenciaTarde->estado == 'presente') $totalPresente++;
                                    if($asistenciaManana && $asistenciaManana->estado == 'ausente') $totalAusente++;
                                    if($asistenciaTarde && $asistenciaTarde->estado == 'ausente') $totalAusente++;
                                    if($asistenciaManana && $asistenciaManana->estado == 'justificado') $totalJustificado++;
                                    if($asistenciaTarde && $asistenciaTarde->estado == 'justificado') $totalJustificado++;
                                }
                            }
                        @endphp
                        <tr>
                            <!-- Columna del nombre del atleta -->
                            <td style="position: sticky; left: 0; background: white; z-index: 10;">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <strong class="atleta-nombre">{{ $atleta->nombre }} {{ $atleta->apellido }}</strong>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Celdas de asistencia por día -->
                            @foreach($diasMes as $dia)
                            @php
                                $asistencia = $asistencias[$atleta->id][$dia['fecha']][$turno][0] ?? null;
                                $estado = $asistencia->estado ?? null;
                                $esDiaInhabil = $dia['es_domingo'] || $dia['es_dia_inhabil'];
                            @endphp
                            <td class="text-center align-middle {{ $esDiaInhabil ? 'bg-light' : '' }}"
                                title="{{ $dia['dia_semana'] }} {{ $dia['dia'] }}">
                                @if(!$esDiaInhabil && $estado)
                                    @if($estado == 'presente')
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    @elseif($estado == 'ausente')
                                    <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                    @elseif($estado == 'justificado')
                                    <i class="bi bi-exclamation-circle-fill text-warning fs-5"></i>
                                    @else
                                    <i class="bi bi-circle text-muted fs-5"></i>
                                    @endif
                                @endif
                            </td>
                            @endforeach
                            
                            <!-- Columna de totales -->
                            <td class="text-center align-middle bg-light" 
                                style="position: sticky; right: 0; background: #f8f9fa!important;">
                                <span class="badge bg-success">{{ $totalPresente }}</span>
                                <span class="badge bg-danger">{{ $totalAusente }}</span>
                                <span class="badge bg-warning">{{ $totalJustificado }}</span>
                                <div class="small mt-1">
                                    @php
                                        $totalDiasHabiles = $totalDias * ($atleta->grupo == 'Federados' ? 2 : 1);
                                        $porcentaje = $totalDiasHabiles > 0 ? round(($totalPresente / $totalDiasHabiles) * 100) : 0;
                                    @endphp
                                    {{ $porcentaje }}%
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
@endsection