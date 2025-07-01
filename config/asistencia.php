<?php

return [
    'GRUPOS_DISPONIBLES' => ['Federados', 'Novatos', 'Juniors', 'Principiantes'],
    'TURNOS_DISPONIBLES' => ['mañana', 'tarde'],
    'ESTADOS_DISPONIBLES' => ['presente', 'ausente', 'justificado'],
    
    'DIAS_INACTIVOS' => [
        'Federados' => [Carbon\Carbon::SUNDAY],
        'Novatos' => [Carbon\Carbon::SUNDAY],
        'Juniors' => [
            Carbon\Carbon::MONDAY, 
            Carbon\Carbon::WEDNESDAY, 
            Carbon\Carbon::FRIDAY, 
            Carbon\Carbon::SUNDAY
        ],
        'Principiantes' => [
            Carbon\Carbon::MONDAY, 
            Carbon\Carbon::WEDNESDAY, 
            Carbon\Carbon::FRIDAY, 
            Carbon\Carbon::SUNDAY
        ]
    ],
    
    'TURNOS_POR_GRUPO' => [
        'Federados' => ['mañana', 'tarde'],
        'Novatos' => ['tarde'],
        'Juniors' => ['tarde'],
        'Principiantes' => ['tarde']
    ]
];