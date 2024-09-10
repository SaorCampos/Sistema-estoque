<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilPermissaoSeeder extends Seeder
{
    public function run()
    {
        $permissaoIds = DB::table('permissao')->pluck('id');
        $adminId = DB::table('perfil')->where('nome', '=', 'Admin')->first()->id;
        $perfilPermissoes = $permissaoIds->map(function ($permissaoId) use ($adminId) {
            return [
                'perfil_id' => $adminId,
                'permissao_id' => $permissaoId,
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
            ];
        });
        DB::table('perfil_permissao')->insert($perfilPermissoes->toArray());
    }
}
