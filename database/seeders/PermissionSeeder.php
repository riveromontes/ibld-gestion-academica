<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$permissions = [
        	//Permisos admin
        	'Cambiar status',
            //Permiso de usuarios
    		'Crear usuario','Editar usuario','Borrar usuario','Mostrar usuarios'
        ];

        foreach ($permissions as $permission) {
    		Permission::create([
    		'name' => $permission,
    		'guard_name' => 'api'
    	]);
    	}

    	$admin = Role::create([
            'name' => 'Admin',
            'guard_name' => 'api'
        ]);
        $profesor = Role::create([
            'name' => 'Profesor',
            'guard_name' => 'api'
        ]);

        $estudiante = Role::create([
            'name' => 'Estudiante',
            'guard_name' => 'api'
        ]);

        $coordinador = Role::create([
            'name' => 'Coordinador',
            'guard_name' => 'api'
        ]);

        $userPermissions= ['Crear usuario','Editar usuario','Borrar usuario','Mostrar usuarios'];

        $admin->givePermissionTo([$userPermissions]);
        $coordinador->givePermissionTo([$userPermissions]);

    }
}
