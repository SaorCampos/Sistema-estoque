<?php

namespace Tests\Feature\Item;

use App\Models\Items;
use Tests\TestCase;
use App\Models\User;
use App\Models\Perfil;
use App\Models\Permissao;
use Illuminate\Support\Str;
use App\Models\PerfilPermissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemUpdateTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_updateItem_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Arrange
        $data = [
            'id' => (string)Str::uuid(),
            'categoria' => $this->faker->word(),
            'subCategoria' => $this->faker()->sentence(),
            'descricao' => $this->faker()->sentence(),
        ];
        // Act
        $response = $this->putJson('/api/itens/alterar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_updateItem_with_beingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        $data = [
            'id' => (string)Str::uuid(),
            'categoria' => $this->faker->word(),
            'subCategoria' => $this->faker()->sentence(),
            'descricao' => $this->faker()->sentence(),
        ];
        // Act
        $response = $this->putJson('/api/itens/alterar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_updateItem_with_beingAuthenticated_andInvalidId_returnsNotFound(): void
    {
        // Arrange
        User::truncate();
        Items::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        $user = User::factory()->createOne();
        $perfil = Perfil::where('id', '=', (string)$user->perfil_id)->first();
        $this->seed(PermissaoSeeder::class);
        $permissaoIds = DB::table('permissao')->pluck('id');
        $perfilUsuarioId = DB::table('perfil')->where('nome', '=', (string)$perfil->nome)->first()->id;
        $perfilPermissoes = $permissaoIds->map(function ($permissaoId) use ($perfilUsuarioId) {
            return [
                'perfil_id' => $perfilUsuarioId,
                'permissao_id' => $permissaoId,
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
            ];
        });
        DB::table('perfil_permissao')->insert($perfilPermissoes->toArray());
        $this->actingAs($user, 'jwt');
        $data = [
            'id' => (string)Str::uuid(),
            'categoria' => $this->faker->word(),
            'subCategoria' => $this->faker()->sentence(),
            'descricao' => $this->faker()->sentence(),
        ];
        // Act
        $response = $this->putJson('/api/itens/alterar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertNotFound();
        $this->assertEquals('Item não encotrado.', $responseBody['message']);
    }
    public function test_updateItem_with_beingAuthenticated_andValidId_returnsOk(): void
    {
        // Arrange
        User::truncate();
        Items::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        $user = User::factory()->createOne();
        $perfil = Perfil::where('id', '=', (string)$user->perfil_id)->first();
        $this->seed(PermissaoSeeder::class);
        $permissaoIds = DB::table('permissao')->pluck('id');
        $perfilUsuarioId = DB::table('perfil')->where('nome', '=', (string)$perfil->nome)->first()->id;
        $perfilPermissoes = $permissaoIds->map(function ($permissaoId) use ($perfilUsuarioId) {
            return [
                'perfil_id' => $perfilUsuarioId,
                'permissao_id' => $permissaoId,
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
            ];
        });
        DB::table('perfil_permissao')->insert($perfilPermissoes->toArray());
        $this->actingAs($user, 'jwt');
        $item = Items::factory()->createOne();
        $data = [
            'id' => (string)$item->id,
            'categoria' => $this->faker->word(),
            'subCategoria' => $this->faker()->sentence(),
            'descricao' => $this->faker()->sentence(),
        ];
        // Act
        $response = $this->putJson('/api/itens/alterar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $this->assertEquals('Item atualizado com sucesso!', $responseBody['message']);
        $this->assertEquals($data['categoria'], $responseBody['data']['categoria']);
        $this->assertEquals($data['subCategoria'], $responseBody['data']['subCategoria']);
        $this->assertEquals($data['descricao'], $responseBody['data']['descricao']);
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'categoria' => $data['categoria'],
            'sub_categoria' => $data['subCategoria'],
            'descricao' => $data['descricao'],
        ]);
    }
}
