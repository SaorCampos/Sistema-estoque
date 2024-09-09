<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Perfil;
use App\Models\User;
use App\Models\UsuarioPerfil;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $perfil = Perfil::factory()->create([
            'id' => Str::uuid(),
             'nome' => 'Admin',
             'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
             'updated_at' =>  fake()->dateTimeBetween('-1 year', 'now'),
             'deleted_at' => null,
             'criado_por' => 'Admin',
             'atualizado_por' => 'Admin',
        ]);
       $usuario = User::factory()->create([
            'id' => Str::uuid(),
            'perfil_id' => $perfil->id,
            'name' => 'Admin',
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'remember_token' => Str::random(10),
            'deleted_at' => null,
            'criado_por' => 'Admin',
            'atualizado_por' => 'Admin',
       ]);
    }
}
