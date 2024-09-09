<?php

namespace Tests\Unit\Repositories;

use App\Core\ApplicationModels\Pagination;
use App\Core\Dtos\PerfilDto;
use Tests\TestCase;
use App\Models\Perfil;
use App\Http\Requests\Perfil\PerfilListingRequest;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Data\Repositories\Perfil\PerfilRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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
}
