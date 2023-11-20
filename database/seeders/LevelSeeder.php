<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Level;
class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$basico = Level::create([
    		'nombre' => 'Básico',
    	]);
    	$basico = Level::create([
    		'nombre' => 'Bachillerato',
    	]);
    	$basico = Level::create([
    		'nombre' => 'Técnico Superior',
    	]);
    	$basico = Level::create([
    		'nombre' => 'Licenciatura',
    	]);

    }
}
