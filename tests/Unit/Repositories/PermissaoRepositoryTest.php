<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Permissao;
use App\Core\Dtos\PermissaoDto;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use App\Core\ApplicationModels\Pagination;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Data\Repositories\Permissao\PermissaoRepository;
use App\Http\Requests\Permissao\PermissaoListingRequest;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use Illuminate\Support\Collection;

class PermissaoRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private IPermissaoRepository $sut;

    public function test_getPermissoes_returnsPaginatedList(): void
    {
        // Arrange
        Permissao::truncate();
        $this->seed(PermissaoSeeder::class);
        $request = new PermissaoListingRequest();
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new PermissaoRepository();
        // Act
        $response = $this->sut->getPermissoes($request, $pagination);
        $this->assertEquals($pagination->page, $response->pagination->page);
        $this->assertEquals($pagination->perPage, $response->pagination->perPage);
        $this->assertIsArray($response->list);
        foreach($response->list as $instance){
            $this->assertInstanceOf(PermissaoDto::class, $instance);
        }
    }
    public function test_createPermissao_returnsPermissao(): void
    {
        // Arrange
        Permissao::truncate();
        $permissao = Permissao::factory()->make();
        $this->sut = new PermissaoRepository();
        // Act
        $response = $this->sut->createPermissao($permissao);
        // Assert
        $this->assertInstanceOf(Permissao::class, $response);
    }
    public function test_updatePermissao_returnsTrue(): void
    {
        // Arrange
        Permissao::truncate();
        $permissao = Permissao::factory()->create();
        $permissao->nome = 'Teste';
        $this->sut = new PermissaoRepository();
        // Act
        $response = $this->sut->updatePermissao($permissao->id, $permissao);
        // Assert
        $this->assertTrue($response);
    }
    public function test_deletePermissao_returnsTrue(): void
    {
        // Arrange
        Permissao::truncate();
        $permissao = Permissao::factory()->create();
        $this->sut = new PermissaoRepository();
        // Act
        $response = $this->sut->deletePermissao($permissao->id);
        // Assert
        $this->assertTrue($response);
    }
    public function test_getPermissoesByIdList_returnsCollection(): void
    {
        // Arrange
        Permissao::truncate();
        $this->seed(PermissaoSeeder::class);
        $permissao1 = DB::table('permissao')->where('nome', '=', 'Editar Perfis')->first()->id;
        $permissao2 = DB::table('permissao')->where('nome', '=', 'Deletar Perfis')->first()->id;
        $ids = [$permissao1, $permissao2];
        $this->sut = new PermissaoRepository();
        // Act
        $response = $this->sut->getPermissoesByIdList($ids);
        // Assert
        $this->assertInstanceOf(Collection::class, $response);
        foreach($response as $instance){
            $this->assertInstanceOf(PermissaoDto::class, $instance);
        }
    }
}
