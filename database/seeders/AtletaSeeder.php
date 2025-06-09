<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Atleta;
use App\Models\User;

class AtletaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
      public function run()
    {
        // Obtener el usuario administrador (el que creamos en UserSeeder)
        $admin = User::where('email', 'hgtreminio@nads.com')->first();

        // Datos de ejemplo para cada grupo
        $grupos = [
            'Federados' => [
                ['Juan', 'Pérez', 18, true],
                ['María', 'Gómez', 17, false]
            ],
            'Novatos' => [
                ['Carlos', 'López', 15, false],
                ['Ana', 'Martínez', 16, true]
            ],
            'Juniors' => [
                ['Luis', 'Hernández', 14, false],
                ['Sofía', 'Díaz', 13, true]
            ],
            'Principiantes' => [
                ['Pedro', 'Sánchez', 12, false],
                ['Laura', 'Ramírez', 11, true]
            ]
        ];

        // Crear atletas para cada grupo
        foreach ($grupos as $grupo => $atletas) {
            foreach ($atletas as $datosAtleta) {
                Atleta::create([
                    'nombre' => $datosAtleta[0],
                    'apellido' => $datosAtleta[1],
                    'edad' => $datosAtleta[2],
                    'becado' => $datosAtleta[3],
                    'grupo' => $grupo,
                    'user_id' => $admin->id, // Asigna al administrador como creador
                    'foto' => null // Puedes añadir fotos luego
                ]);
            }
        }

        $this->command->info('¡Atletas creados exitosamente!');
    }
}
