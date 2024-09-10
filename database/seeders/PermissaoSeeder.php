<?php

namespace Database\Seeders;

use App\Models\Permissao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissoes = [
            [
                'id' => Str::uuid(),
                'nome' => 'Listar Perfis',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],
            [
                'id' => Str::uuid(),
                'nome' => 'Criar Perfis',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],
            [
                'id' => Str::uuid(),
                'nome' => 'Editar Perfis',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],
            [
                'id' => Str::uuid(),
                'nome' => 'Deletar Perfis',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],
            [
                'id' => Str::uuid(),
                'nome' => 'Listar Permissões',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],
            [
                'id' => Str::uuid(),
                'nome' => 'Criar Permissões',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],
            [
                'id' => Str::uuid(),
                'nome' => 'Editar Permissões',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],
            [
                'id' => Str::uuid(),
                'nome' => 'Deletar Permissões',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],
            [
                'id' => Str::uuid(),
                'nome' => 'Listar Usuários',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],
            [
                'id' => Str::uuid(),
                'nome' => 'Criar Usuários',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],
            [
                'id' => Str::uuid(),
                'nome' => 'Editar Usuários',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],
            [
                'id' => Str::uuid(),
                'nome' => 'Deletar Usuários',
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
                'deletado_em' => null
            ],

        ];
        foreach ($permissoes as $permissao) {
            if (!Permissao::query()->where('nome', '=', $permissao['nome'])->first())
                DB::table('permissao')->insert($permissao);
        }
    }
}
