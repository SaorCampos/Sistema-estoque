<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Items;
use App\Core\Dtos\ItemDto;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Core\ApplicationModels\Pagination;
use App\Data\Repositories\Item\ItemRepository;
use App\Core\Repositories\Item\IItemRepository;
use App\Http\Requests\Item\ItemsListingRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private IItemRepository $sut;

    public function test_getItens_returnsPaginatedList(): void
    {
        // Arrange
        Items::truncate();
        Items::factory(100)->create();
        $request = new ItemsListingRequest();
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->getAllItems($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(ItemDto::class, $item);
        }
    }
    public function test_getItens_usingFilterNome_returnsPaginatedList(): void
    {
        // Arrange
        Items::truncate();
        Items::factory(100)->create();
        $item = Items::factory()->createOne();
        $request = new ItemsListingRequest();
        $request->nome = substr($item->nome, 2);
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->getAllItems($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(ItemDto::class, $item);
            $this->assertStringContainsStringIgnoringCase($request->nome, $item->nome);
        }
    }
    public function test_getItens_usingFilterCategoria_returnsPaginatedList(): void
    {
        // Arrange
        Items::truncate();
        Items::factory(100)->create();
        $item = Items::factory()->createOne();
        $request = new ItemsListingRequest();
        $request->categoria = $item->categoria;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->getAllItems($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(ItemDto::class, $item);
            $this->assertEquals($request->categoria, $item->categoria);
        }
    }
    public function test_getItens_usingFilterDescricao_returnsPaginatedList(): void
    {
        // Arrange
        Items::truncate();
        Items::factory(100)->create();
        $item = Items::factory()->createOne();
        $request = new ItemsListingRequest();
        $request->descricao = substr($item->descricao, 2);
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->getAllItems($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(ItemDto::class, $item);
            $this->assertStringContainsStringIgnoringCase($request->descricao, $item->descricao);
        }
    }
    public function test_getItens_usingFilterCriadoPor_returnsPaginatedList(): void
    {
        // Arrange
        Items::truncate();
        Items::factory(100)->create();
        $item = Items::factory()->createOne();
        $user = DB::table('users')->where('id', $item->user_id)->first();
        $request = new ItemsListingRequest();
        $request->criadoPor = $user->name;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->getAllItems($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(ItemDto::class, $item);
            $this->assertStringContainsStringIgnoringCase($request->criadoPor, $item->criadoPor);
        }
    }
    public function test_getItemById_onValidData_returnsItemDto(): void
    {
        // Arrange
        Items::truncate();
        $item = Items::factory()->createOne();
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->getItemById($item->id);
        // Assert
        $this->assertInstanceOf(ItemDto::class, $result);
        $this->assertEquals($item->id, $result->id);
    }
    public function test_getItemById_onInvalidData_returnsNull(): void
    {
        // Arrange
        Items::truncate();
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->getItemById((string)Str::uuid());
        // Assert
        $this->assertNull($result);
    }
    public function test_createItem_onValidData_returnsItems(): void
    {
        // Arrange
        Items::truncate();
        $item = Items::factory()->makeOne();
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->createItem($item);
        // Assert
        $this->assertInstanceOf(Items::class, $result);
        $this->assertEquals($item->nome, $result->nome);
        $this->assertEquals($item->categoria, $result->categoria);
        $this->assertEquals($item->descricao, $result->descricao);
        $this->assertEquals($item->user_id, $result->user_id);
    }
    public function test_updateItem_onValidData_returnsTrue(): void
    {
        // Arrange
        Items::truncate();
        $item = Items::factory()->createOne();
        $item->nome = fake()->name();
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->updateItem($item->id, $item);
        // Assert
        $this->assertTrue($result);
    }
    public function test_updateItem_onInvalidData_returnsFalse(): void
    {
        // Arrange
        Items::truncate();
        $item = Items::factory()->makeOne();
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->updateItem((string)Str::uuid(), $item);
        // Assert
        $this->assertFalse($result);
    }
    public function test_deleteItem_onValidData_returnsTrue(): void
    {
        // Arrange
        Items::truncate();
        $item = Items::factory()->createOne();
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->deleteItem($item->id);
        // Assert
        $this->assertTrue($result);
    }
    public function test_deleteItem_onInvalidData_returnsFalse(): void
    {
        // Arrange
        Items::truncate();
        $this->sut = new ItemRepository();
        // Act
        $result = $this->sut->deleteItem((string)Str::uuid());
        // Assert
        $this->assertFalse($result);
    }
}
