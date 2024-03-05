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
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        TipoUsuario::factory()->times(3)->create();
        User::factory()->times(5)->create();
        Modulo::factory()->times(3)->create();
        PermisoUsuario::factory()->times(4)->create();
        Comite::factory()->times(3)->create();
        GestionComite::factory()->times(5)->create();
        Compromiso::factory()->times(5)->create();
        
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
