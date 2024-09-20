<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Perfil;
use App\Models\Permissao;
use Illuminate\Support\Str;
use App\Core\Dtos\PerfilDto;
use App\Models\PerfilPermissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use App\Core\ApplicationModels\Pagination;
use App\Data\Repositories\Perfil\PerfilRepository;
use App\Http\Requests\Perfil\PerfilListingRequest;
use App\Core\Repositories\Perfil\IPerfilRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;

class PerfilRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private  IPerfilRepository $sut;

    public function test_getPerfis_returnsPaginatedList(): void
    {
        // Arrange
        Perfil::truncate();
        Perfil::factory(100)->create();
        $request = new PerfilListingRequest();
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new PerfilRepository();
        // Act
        $response = $this->sut->getPerfis($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $response->pagination->page);
        $this->assertEquals($pagination->perPage, $response->pagination->perPage);
        $this->assertIsArray($response->list);
        foreach($response->list as $instance){
            $this->assertInstanceOf(PerfilDto::class, $instance);
        }
    }
    public function test_getPerfis_usingFilterNome_returnsPaginatedList(): void
    {
        // Arrange
        Perfil::truncate();
        Perfil::factory(100)->create();
        $perfil = Perfil::factory()->createOne();
        $request = new PerfilListingRequest();
        $request->nome = substr($perfil->nome, 2);
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new PerfilRepository();
        // Act
        $response = $this->sut->getPerfis($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $response->pagination->page);
        $this->assertEquals($pagination->perPage, $response->pagination->perPage);
        $this->assertIsArray($response->list);
        foreach($response->list as $instance){
            $this->assertInstanceOf(PerfilDto::class, $instance);
            $this->assertStringContainsStringIgnoringCase($request->nome, $instance->nome);
        }
    }
    public function test_getPerfis_usingFilterPerfilId_returnsPaginatedList(): void
    {
        // Arrange
        Perfil::truncate();
        Perfil::factory(100)->create();
        $perfil = Perfil::factory()->createOne();
        $request = new PerfilListingRequest();
        $request->perfilId = (string)$perfil->id;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new PerfilRepository();
        // Act
        $response = $this->sut->getPerfis($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $response->pagination->page);
        $this->assertEquals($pagination->perPage, $response->pagination->perPage);
        $this->assertIsArray($response->list);
        foreach($response->list as $instance){
            $this->assertInstanceOf(PerfilDto::class, $instance);
            $this->assertEquals($request->perfilId, $instance->id);
        }
    }
    public function test_getPerfilById_onExistingRecords_returnsPerfilDto(): void
    {
        // Arrange
        Perfil::truncate();
        $perfil = Perfil::factory()->createOne();
        $this->sut = new PerfilRepository();
        // Act
        $response = $this->sut->getPerfilById($perfil->id);
        // Assert
        $this->assertInstanceOf(PerfilDto::class, $response);
        $this->assertEquals($perfil->id, $response->id);
    }
    public function test_getPerfilById_onNonExistingRecords_returnsNull(): void
    {
        // Arrange
        Perfil::truncate();
        $this->sut = new PerfilRepository();
        $id = (string)Str::uuid();
        // Act
        $response = $this->sut->getPerfilById($id);
        // Assert
        $this->assertNull($response);
    }
    public function test_getPermissoesByPerfilId_onExistingRecords_returnsCollection(): void
    {
        // Arrange
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        $perfil = Perfil::factory()->createOne();
        $this->seed(PermissaoSeeder::class);
        $permissaoIds = DB::table('permissao')->pluck('id');
        $perfilPermissoes = $permissaoIds->map(function ($permissaoId) use ($perfil) {
            return [
                'perfil_id' => $perfil->id,
                'permissao_id' => $permissaoId,
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
            ];
        });
        DB::table('perfil_permissao')->insert($perfilPermissoes->toArray());
        $this->sut = new PerfilRepository();
        // Act
        $response = $this->sut->getPermissoesByPerfilId($perfil->id);
        // Assert
        $this->assertInstanceOf(Collection::class, $response);
    }
}
