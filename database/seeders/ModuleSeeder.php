<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Module;
class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Module::create([
    		'nombre' => 'Introductorio y habitos de estudio.',
    		'level_id' => 1
    	]);
    }
}
