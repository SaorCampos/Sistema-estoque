<?php

namespace Tests\Unit\Services\Item;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Models\Items;
use App\Core\Dtos\ItemDto;
use Mockery\MockInterface;
use Tests\Utils\TestUtils;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Core\ApplicationModels\JwtToken;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\Item\ItemUpdateRequest;
use App\Core\Services\Item\IItemUpdateService;
use App\Core\Repositories\Item\IItemRepository;
use App\Domain\Services\Item\ItemUpdateService;
use App\Core\ApplicationModels\JwtTokenProvider;
use Illuminate\Http\Exceptions\HttpResponseException;

class ItemUpdateServiceTest extends TestCase
{
    use WithFaker;

    private IItemUpdateService $sut;

    public function test_updateItem_with_nonExistingItemThrowsResponseException(): void
    {
        // Assert
        $jwtToken = Mockery::mock(JwtToken::class, function (MockInterface $mock){
            $mock->shouldReceive('validateRole')
                ->once()
                ->with('Editar Items');
        });
        /** @var JwtTokenProvider $jwtTokenProvider */
        $jwtTokenProvider = Mockery::mock(JwtTokenProvider::class, function (MockInterface $mock) use ($jwtToken) {
            $mock->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        });
        $request = new ItemUpdateRequest();
        $request->id = (string)Str::uuid();
        $request->categoria = $this->faker->word();
        $request->descricao = $this->faker->text();
        $request->subCategoria = $this->faker->word();
        /** @var IItemRepository $itemRepository */
        $itemRepository = Mockery::mock(IItemRepository::class, function (MockInterface $mock) use ($request) {
            $mock->shouldReceive('getItemById')
                ->with($request->id)
                ->andReturn(null);
        });
        $this->sut = new ItemUpdateService($itemRepository, $jwtTokenProvider);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $this->sut->updateItem($request);
    }
    public function test_updateItem_with_existingItemReturnsTrue(): void
    {
        // Assert
        $jwtToken = Mockery::mock(JwtToken::class, function (MockInterface $mock){
            $mock->shouldReceive('validateRole')
                ->once()
                ->with('Editar Items');
        });
        /** @var JwtTokenProvider $jwtTokenProvider */
        $jwtTokenProvider = Mockery::mock(JwtTokenProvider::class, function (MockInterface $mock) use ($jwtToken) {
            $mock->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        });
        $item = Items::factory()->makeOne(['id' => (string)Str::uuid()]);
        $request = new ItemUpdateRequest();
        $request->id = $item->id;
        $request->categoria = $this->faker->word();
        $request->descricao = $this->faker->text();
        $request->subCategoria = $this->faker->word();
        $itemDto = TestUtils::mockObj(ItemDto::class);
        $itemDto->id = $item->id;
        $expectedResult = $itemDto;
        $expectedResult->categoria = $request->categoria;
        $expectedResult->descricao = $request->descricao;
        $expectedResult->subCategoria = $request->subCategoria;
        /** @var IItemRepository $itemRepository */
        $itemRepository = Mockery::mock(IItemRepository::class, function (MockInterface $mock) use ($request, $itemDto, $expectedResult) {
            $mock->shouldReceive('getItemById')
                ->with($request->id)
                ->andReturn($itemDto);
            $mock->shouldReceive('updateItem')
                ->andReturn(true);
            $mock->shouldReceive('getItemById')
                ->with($request->id)
                ->andReturn($expectedResult);
        });
        $this->sut = new ItemUpdateService($itemRepository, $jwtTokenProvider);
        // Act
        $result = $this->sut->updateItem($request);
        // Assert
        $this->assertEquals($expectedResult, $result);
    }
}
