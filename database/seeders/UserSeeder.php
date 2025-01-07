<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $profesor = User::create([
            'name' => 'Nancy Pernia de Mejias',
            'email' => 'nancy.pernia@ibldvenezuela.com',
            'password' => '12345'
        ]);
        
       /*  $profesor = User::create([
            'name' => 'Programador Web',
            'email' => 'programador.web@ibldvenezuela.com',
            'password' => '12345'
        ]); */

        $estudiante = User::create([
            'name' => 'Jesus Rivera',
            'email' => 'jesusfriverar@gmail.com',
            'password' => '12345'
        ]);

        $coordinador = User::create([
            'name' => 'Fernando Arreaza',
            'email' => 'augustfer1@gmail.com',
            'password' => '12345'
        ]);

         $dataProfesor = [
            "nombre" => 'Nancy',
            "apellido" => 'Pernia de Mejias',
            "cedula" => 'V6274821',
            "correo" => 'nancy.pernia@ibldvenezuela.com',
            "direccion" => 'Ibld Caripe',
            "telefono" => '+58414-9975738'
        ];
        $profesor->person()->create($dataProfesor);

        /* $dataProfesor = [
            "nombre" => 'Programador',
            "apellido" => 'Web',
            "cedula" => 'V12345',
            "correo" => 'programador.web@gmail.com',
            "direccion" => 'Puerto La Cruz',
            "telefono" => '+12345'
        ];
        $profesor->person()->create($dataProfesor); */

        $dataEstudiante = [
            "nombre" => 'Jesus',
            "apellido" => 'Rivera Rondon',
            "cedula" => 'V24577085',
            "correo" => 'jesusfriverar@gmail.com',
            "direccion" => 'Barcelona',
            "telefono" => '+584264840899'
        ];
        $estudiante->person()->create($dataEstudiante);

        $dataCoordinador = [
            "nombre" => 'Fernando Augusto',
            "apellido" => 'Arreaza',
            "cedula" => '2000000',
            "correo" => 'augustfer1@gmail.com',
            "direccion" => 'Barcelona',
            "telefono" => '+5804244645465'
        ];
        $coordinador->person()->create($dataCoordinador);



        $profesor->assignRole('Profesor');
        $estudiante->assignRole('Estudiante');
        $coordinador->assignRole('Coordinador');
    }
}
