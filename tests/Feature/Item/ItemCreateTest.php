<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use App\Models\User;
use App\Models\Perfil;
use App\Models\Permissao;
use App\Models\PerfilPermissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemCreateTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_createItem_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Arrange
        $data = [
            'nome' => $this->faker->name(),
            'categoria' => $this->faker->word(),
            'subCategoria' => $this->faker()->sentence(),
            'descricao' => $this->faker()->sentence(),
            'quantidade' => 1,
        ];
        // Act
        $response = $this->postJson('api/itens/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_createItem__with_beingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        $data = [
            'nome' => $this->faker->name(),
            'categoria' => $this->faker->word(),
            'subCategoria' => $this->faker()->sentence(),
            'descricao' => $this->faker()->sentence(),
            'quantidade' => 1,
        ];
        // Act
        $response = $this->postJson('api/itens/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_createItem__with_beingAuthenticated_andInvalidQuantidade_returnsBadRequest(): void
    {
        // Arrange
        User::truncate();
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
        $quantidade = rand(-1, 0);
        $data = [
            'nome' => $this->faker->name(),
            'categoria' => $this->faker->word(),
            'subCategoria' => $this->faker()->sentence(),
            'descricao' => $this->faker()->sentence(),
            'quantidade' => $quantidade,
        ];
        // Act
        $response = $this->postJson('api/itens/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertBadRequest();
    }
    public function test_createItem__with_beingAuthenticated_andValidData_returnsOk(): void
    {
        // Arrange
        User::truncate();
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
            'nome' => $this->faker->name(),
            'categoria' => $this->faker->word(),
            'subCategoria' => $this->faker()->sentence(),
            'descricao' => $this->faker()->sentence(),
            'quantidade' => 1,
        ];
        // Act
        $response = $this->postJson('api/itens/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $this->assertEquals($data['nome'], $responseBody['data']['nome']);
        $this->assertEquals($data['categoria'], $responseBody['data']['categoria']);
        $this->assertEquals($data['subCategoria'], $responseBody['data']['subCategoria']);
        $this->assertEquals($data['descricao'], $responseBody['data']['descricao']);
        $this->assertEquals($data['quantidade'], $responseBody['data']['quantidadeEstoque']);
        $this->assertEquals($user->name, $responseBody['data']['criadoPor']);
        $this->assertDatabaseHas('items',[
            'nome' => $data['nome'],
            'categoria' => $data['categoria'],
            'sub_categoria' => $data['subCategoria'],
            'descricao' => $data['descricao'],
            'estoque' => $data['quantidade'],
            'user_id' => $user->id,
        ]);
    }
}
