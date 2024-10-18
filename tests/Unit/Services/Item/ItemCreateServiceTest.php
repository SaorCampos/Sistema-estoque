<?php

namespace Tests\Unit\Services\Item;

use Tests\TestCase;
use App\Models\User;
use Mockery as Mock;
use App\Models\Items;
use App\Core\Dtos\ItemDto;
use Tests\Utils\TestUtils;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Core\ApplicationModels\JwtToken;
use App\Http\Requests\Item\ItemCreateRequest;
use App\Core\Repositories\Item\IItemRepository;
use App\Domain\Services\Item\ItemCreateService;
use App\Core\ApplicationModels\JwtTokenProvider;

class ItemCreateServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_createItem_withValidData_returnsItemDto(): void
    {
        // Arrange
        $itemRepository = Mock::mock(IItemRepository::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $request = new ItemCreateRequest();
        $request->nome = 'Teste';
        $request->categoria = 'Teste';
        $request->subCategoria = 'Teste';
        $request->descricao = 'Teste';
        $request->quantidade = rand(1, 100);
        $user = User::factory()->makeOne(['id' => (string)Str::uuid()]);
        Auth::shouldReceive('user')->andReturn($user);
        $itemForCreate = new Items($request->all());
        $itemForCreate->user_id = $user->id;
        $expectedCreateResult = Items::factory()->makeOne([
            'id' => (string)Str::uuid(),
            'nome' => $request->nome,
            'categoria' => $request->categoria,
            'subCategoria' => $request->subCategoria,
            'descricao' => $request->descricao,
            'quantidade' => $request->quantidade,
            'user_id' => $itemForCreate->user_id,
        ]);
        $expectedItemDto = TestUtils::mockObj(ItemDto::class);
        $expectedItemDto->id = $expectedCreateResult->id;
        $expectedItemDto->nome = $expectedCreateResult->nome;
        $expectedItemDto->categoria = $expectedCreateResult->categoria;
        $expectedItemDto->subCategoria = $expectedCreateResult->subCategoria;
        $expectedItemDto->descricao = $expectedCreateResult->descricao;
        $expectedItemDto->quantidade = $expectedCreateResult->quantidade;
        $expectedItemDto->criadoPor = $jwtToken->userName;
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Criar Items')
            ->once();
        $itemRepository->shouldReceive('createItem')
        ->with(Mock::on(function ($itemForCreate) use ($request, $user) {
            return $itemForCreate->nome === $request->nome &&
                    $itemForCreate->categoria === $request->categoria &&
                    $itemForCreate->sub_categoria === $request->subCategoria &&
                    $itemForCreate->descricao === $request->descricao &&
                    $itemForCreate->estoque === $request->quantidade &&
                    $itemForCreate->user_id === $user->id;
        }))
        ->once()
        ->andReturn($expectedCreateResult);
        $itemRepository->shouldReceive('getItemById')
            ->with($expectedCreateResult->id)
            ->once()
            ->andReturn($expectedItemDto);
        $itemCreateService = new ItemCreateService(
            $itemRepository,
            $jwtTokenProvider
        );
        // Act
        $result = $itemCreateService->createItem($request);
        // Assert
        $this->assertEquals($expectedItemDto, $result);
    }
}
