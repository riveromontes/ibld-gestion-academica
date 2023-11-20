<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Chair;
use Illuminate\Support\Facades\DB;

class ChairSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $chair = Chair::create([
    		'nombre' => 'Seccion 5 - Virtual',
    		'fecha_inicio' => '2021-01-08 03:02:31',
    		'ubicacion' => 'Barcelona',
    		'coordinador' => 'Pedro',
    		'inscripciones'=>true
    	]);

    	DB::table('chair_module')->insert([
    		'chair_id'=>$chair->id,
    		'module_id'=>1,
    		'fecha_apertura' => '2021-01-08 03:02:31',
    		'person_id' => 2
    	]);
    }
}
