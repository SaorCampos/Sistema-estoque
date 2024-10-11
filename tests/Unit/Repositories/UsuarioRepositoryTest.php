<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use App\Core\Dtos\UsuarioDto;
use App\Core\ApplicationModels\Pagination;
use Illuminate\Foundation\Testing\WithFaker;
use App\Data\Repositories\Usuario\UsuarioRepository;
use App\Http\Requests\Usuario\UsuarioListingRequest;
use App\Core\Repositories\Usuario\IUsuarioRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;

class UsuarioRepositoryTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    private IUsuarioRepository $sut;

    public function test_getUsuarios_returnsPaginatedList(): void
    {
        // Arrange
        User::truncate();
        User::factory(100)->create();
        $request = new UsuarioListingRequest();
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new UsuarioRepository();
        // Act
        $response = $this->sut->getUsuarios($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $response->pagination->page);
        $this->assertEquals($pagination->perPage, $response->pagination->perPage);
        $this->assertIsArray($response->list);
        $this->assertNotEmpty($response->list);
        foreach ($response->list as $usuario) {
            $this->assertInstanceOf(UsuarioDto::class, $usuario);
        }
    }
    public function test_getUsarios_usingFilterNome_returnsPaginatedList(): void
    {
        // Arrange
        User::truncate();
        User::factory(100)->create();
        $usuario = User::factory()->createOne();
        $request = new UsuarioListingRequest();
        $request->nome =substr($usuario->name, 2);
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new UsuarioRepository();
        // Act
        $response = $this->sut->getUsuarios($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $response->pagination->page);
        $this->assertEquals($pagination->perPage, $response->pagination->perPage);
        $this->assertIsArray($response->list);
        $this->assertNotEmpty($response->list);
        foreach ($response->list as $usuario) {
            $this->assertInstanceOf(UsuarioDto::class, $usuario);
            $this->assertStringContainsStringIgnoringCase($request->nome, $usuario->nome);
        }
    }
    public function test_getUsuarios_usingFilterUsuarioId_returnsPPaginnatedList(): void
    {
        // Arrange
        User::truncate();
        User::factory(100)->create();
        $usuario = User::factory()->createOne();
        $request = new UsuarioListingRequest();
        $request->usuarioId = $usuario->id;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new UsuarioRepository();
        // Act
        $response = $this->sut->getUsuarios($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $response->pagination->page);
        $this->assertEquals($pagination->perPage, $response->pagination->perPage);
        $this->assertIsArray($response->list);
        $this->assertNotEmpty($response->list);
        foreach ($response->list as $usuario) {
            $this->assertInstanceOf(UsuarioDto::class, $usuario);
            $this->assertEquals($request->usuarioId, $usuario->id);
        }
    }
    public function test_getUsuarioById_onExistingRecords_returnsUsuarioDto(): void
    {
        // Arrange
        User::truncate();
        $usuario = User::factory()->createOne();
        $this->sut = new UsuarioRepository();
        // Act
        $response = $this->sut->getUsuarioById($usuario->id);
        // Assert
        $this->assertInstanceOf(UsuarioDto::class, $response);
        $this->assertEquals($usuario->id, $response->id);
        $this->assertEquals($usuario->name, $response->nome);
        $this->assertEquals($usuario->email, $response->email);
    }
    public function test_getUsuarioById_onNonExistingRecords_returnsNull(): void
    {
        // Arrange
        User::truncate();
        $this->sut = new UsuarioRepository();
        $id = (string)Str::uuid();
        // Act
        $response = $this->sut->getUsuarioById($id);
        // Assert
        $this->assertNull($response);
    }
    public function test_createUsuario_returnsUserModel(): void
    {
        // Arrange
        User::truncate();
        $this->sut = new UsuarioRepository();
        $usuario = User::factory()->makeOne();

        // Act
        $response = $this->sut->createUsuario($usuario);
        // Assert
        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($usuario['name'], $response->name);
        $this->assertEquals($usuario['email'], $response->email);
    }
    public function test_updateUsuario_returnsTrue(): void
    {
        // Arrange
        User::truncate();
        $usuario = User::factory()->createOne();
        $this->sut = new UsuarioRepository();
        $usuario->name = $this->faker->name();
        $usuario->email = $this->faker->email();
        // Act
        $response = $this->sut->updateUsuario($usuario->id,$usuario);
        // Assert
        $this->assertTrue($response);
    }
    public function test_updateUsuario_returnsFalse(): void
    {
        // Arrange
        User::truncate();
        $this->sut = new UsuarioRepository();
        $usuario = User::factory()->makeOne();
        $id = (string)Str::uuid();
        // Act
        $response = $this->sut->updateUsuario($id, $usuario);
        // Assert
        $this->assertFalse($response);
    }
    public function test_deleteUsuario_returnsTrue(): void
    {
        // Arrange
        User::truncate();
        $usuario = User::factory()->createOne();
        $this->sut = new UsuarioRepository();
        // Act
        $response = $this->sut->deleteUsuario($usuario->id);
        // Assert
        $this->assertTrue($response);
    }
    public function test_deleteUsuario_returnsFalse(): void
    {
        // Arrange
        User::truncate();
        $this->sut = new UsuarioRepository();
        $id = (string)Str::uuid();
        // Act
        $response = $this->sut->deleteUsuario($id);
        // Assert
        $this->assertFalse($response);
    }
    public function test_getUsuarioByEmail_onExistingRecords_returnsUsuarioDto(): void
    {
        // Arrange
        User::truncate();
        $usuario = User::factory()->createOne();
        $this->sut = new UsuarioRepository();
        // Act
        $response = $this->sut->getUsuarioByEmail($usuario->email);
        // Assert
        $this->assertInstanceOf(UsuarioDto::class, $response);
        $this->assertEquals($usuario->id, $response->id);
        $this->assertEquals($usuario->name, $response->nome);
        $this->assertEquals($usuario->email, $response->email);
    }
    public function test_getUsuarioByEmail_onNonExistingRecords_returnsNull(): void
    {
        // Arrange
        User::truncate();
        $this->sut = new UsuarioRepository();
        $email = $this->faker->email();
        // Act
        $response = $this->sut->getUsuarioByEmail($email);
        // Assert
        $this->assertNull($response);
    }
    public function test_getUsuariosByIdList_returnsCollection(): void
    {
        // Arrange
        User::truncate();
        User::factory(100)->create();
        $ids = User::all()->pluck('id')->toArray();
        $this->sut = new UsuarioRepository();
        // Act
        $response = $this->sut->getUsuariosByIdList($ids);
        // Assert
        $this->assertInstanceOf(Collection::class, $response);
        $this->assertNotEmpty($response);
        foreach ($response as $usuario) {
            $this->assertInstanceOf(UsuarioDto::class, $usuario);
        }
    }
    public function test_reativarUsuario_returnsTrue(): void
    {
        // Arrange
        User::truncate();
        $usuario = User::factory()->createOne(['deletado_em' => now()]);
        $this->sut = new UsuarioRepository();
        // Act
        $response = $this->sut->reativarUsuario($usuario->id);
        // Assert
        $this->assertTrue($response);
    }
}
