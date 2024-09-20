<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Perfil;
use App\Models\Permissao;
use App\Models\PerfilPermissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Core\Repositories\PerfilPermissao\IPerfilPermissaoRepository;
use App\Data\Repositories\PerfilPermissao\PerfilPermissaoRepository;

class PerfilPermissaoRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private IPerfilPermissaoRepository $sut;

    public function test_createPerfilPermissoes_retunsPerfilPermissao(): void
    {
        // Arrange
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        $perfilForUpdate = Perfil::factory()->createOne()->id;
        $this->seed(PermissaoSeeder::class);
        $permissao = DB::table('permissao')->where('nome', '=', 'Editar Perfis')->first()->id;
        $this->sut = new PerfilPermissaoRepository();
        // Act
        $result = $this->sut->createPerfilPermissoes($perfilForUpdate,$permissao);
        // Assert
        $this->assertInstanceOf(PerfilPermissao::class, $result);
    }
    public function test_deletePerfilPermissoes_retunsTrue(): void
    {
        // Arrange
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        $perfilForUpdate = Perfil::factory()->createOne()->id;
        $this->seed(PermissaoSeeder::class);
        $permissao = DB::table('permissao')->where('nome', '=', 'Editar Perfis')->first()->id;
        $this->sut = new PerfilPermissaoRepository();
        $this->sut->createPerfilPermissoes($perfilForUpdate, $permissao);
        // Act
        $result = $this->sut->deletePerfilPermissoes($perfilForUpdate, $permissao);
        // Assert
        $this->assertTrue($result);
    }
}
