<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Comite;
use App\Models\Compromiso;
use App\Models\GestionComite;
use App\Models\Modulo;
use App\Models\PermisoUsuario;
use App\Models\TipoUsuario;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        TipoUsuario::factory()->create(['codigo'=>'001','nombre' => 'Admin','estado'=>'1']);
        User::factory()->create(['name' => 'Admin', 'email' => 'admin@gmail.com','phone'=>'3108765432','cc'=>'123456789','estado'=>'1','tipo_usuario_id'=>'1','password'=>Hash::make('admin'),'password2'=>'admin']);
        Modulo::factory()->create(['codigo'=>'001','nombre' => 'Modulo Comite','estado'=>'1']);
        Modulo::factory()->create(['codigo'=>'002','nombre' => 'Modulo Gestion Comites','estado'=>'1']);
        Modulo::factory()->create(['codigo'=>'003','nombre' => 'Modulo Tipo Usuario','estado'=>'1']);
        Modulo::factory()->create(['codigo'=>'004','nombre' => 'Modulo Usuario','estado'=>'1']);
        PermisoUsuario::factory()->create(['agregar' => 1,'editar' => 1,'eliminar' => 1,'visualizar' => 1,'tipo_usuario' => 1,'modulo_id' => 1]);
        PermisoUsuario::factory()->create(['agregar' => 1,'editar' => 1,'eliminar' => 1,'visualizar' => 1,'tipo_usuario' => 1,'modulo_id' => 2]);
        PermisoUsuario::factory()->create(['agregar' => 1,'editar' => 1,'eliminar' => 1,'visualizar' => 1,'tipo_usuario' => 1,'modulo_id' => 3]);
        PermisoUsuario::factory()->create(['agregar' => 1,'editar' => 1,'eliminar' => 1,'visualizar' => 1,'tipo_usuario' => 1,'modulo_id' => 4]);
       
        //TipoUsuario::factory()->times(3)->create();
        //User::factory()->times(5)->create();
        //Modulo::factory()->times(3)->create();
        //PermisoUsuario::factory()->times(4)->create();
        //Comite::factory()->times(3)->create();
        //GestionComite::factory()->times(5)->create();
        //Compromiso::factory()->times(5)->create();
        
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
