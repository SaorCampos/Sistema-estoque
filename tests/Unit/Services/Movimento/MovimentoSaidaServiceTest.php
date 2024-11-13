<?php

namespace Tests\Unit\Services\Movimento;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Models\Items;
use App\Core\Dtos\ItemDto;
use App\Models\Movimentos;
use Mockery\MockInterface;
use Tests\Utils\TestUtils;
use Illuminate\Support\Str;
use App\Core\Dtos\MovimentoDto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Tests\Utils\DbTransactionsTestUtil;
use App\Core\ApplicationModels\JwtToken;
use Illuminate\Foundation\Testing\WithFaker;
use App\Core\Repositories\Item\IItemRepository;
use App\Core\ApplicationModels\JwtTokenProvider;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\Movimento\MovimentoSaidaRequest;
use App\Core\Services\Movimento\IMovimentoSaidaService;
use App\Domain\Services\Movimento\MovimentoSaidaService;
use App\Core\Repositories\Movimento\IMovimentoRepository;

class MovimentoSaidaServiceTest extends TestCase
{
    use WithFaker;
    private IMovimentoSaidaService $sut;

    public function test_createMovimentacaoSaida_withInvalidItemId_returnsException(): void
    {
        // Assert
        $request = new MovimentoSaidaRequest();
        $request->saidas = [
            [
                'itemId' => (string)Str::uuid(),
                'quantidade' => rand(1, 100),
                'data' => now()->format('Y-m-d'),
                'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                'localDestino' => $this->faker->locale(),
            ]
        ];
        $jwtToken = Mockery::mock(JwtToken::class, function (MockInterface $mock){
            $mock->shouldReceive('validateRole')
                ->once()
                ->with('Criar Movimentações');
        });
        /** @var JwtTokenProvider $jwtTokenProvider */
        $jwtTokenProvider = Mockery::mock(JwtTokenProvider::class, function (MockInterface $mock) use ($jwtToken) {
            $mock->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        });
        /** @var IMovimentoRepository $movimentoRepository */
        $movimentoRepository = Mockery::mock(IMovimentoRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('getMovimentoByNumeroControleSaida')
                ->once()
                ->andReturn(null);
        });
        /** @var IItemRepository $itemRepository */
        $itemRepository = Mockery::mock(IItemRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('getItemById')
                ->once()
                ->andReturn(null);
        });
        $dbTransaction = new DbTransactionsTestUtil();
        $this->sut = new MovimentoSaidaService(
            $jwtTokenProvider,
            $movimentoRepository,
            $itemRepository,
            $dbTransaction
        );
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $this->sut->createMovimentacaoSaida($request);
    }
    public function test_createMovimentacaoSaida_withInvalidItemEstoque_returnsException(): void
    {
        // Assert
        $request = new MovimentoSaidaRequest();
        $itemId = (string)Items::factory()->makeOne(['id' => Str::uuid(), 'estoque' => 1])->id;
        $expectedItemResult = TestUtils::mockObj(ItemDto::class);
        $expectedItemResult->id = $itemId;
        $expectedItemResult->quantidadeEstoque = 1;
        $request->saidas = [
            [
                'itemId' => $itemId,
                'quantidade' => rand(2, 100),
                'data' => now()->format('Y-m-d'),
                'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                'localDestino' => $this->faker->locale(),
            ]
        ];
        $jwtToken = Mockery::mock(JwtToken::class, function (MockInterface $mock){
            $mock->shouldReceive('validateRole')
                ->once()
                ->with('Criar Movimentações');
        });
        /** @var JwtTokenProvider $jwtTokenProvider */
        $jwtTokenProvider = Mockery::mock(JwtTokenProvider::class, function (MockInterface $mock) use ($jwtToken) {
            $mock->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        });
        /** @var IMovimentoRepository $movimentoRepository */
        $movimentoRepository = Mockery::mock(IMovimentoRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('getMovimentoByNumeroControleSaida')
                ->once()
                ->andReturn(null);
        });
        /** @var IItemRepository $itemRepository */
        $itemRepository = Mockery::mock(IItemRepository::class, function (MockInterface $mock) use ($expectedItemResult) {
            $mock->shouldReceive('getItemById')
                ->once()
                ->andReturn($expectedItemResult);
        });
        $dbTransaction = new DbTransactionsTestUtil();
        $this->sut = new MovimentoSaidaService(
            $jwtTokenProvider,
            $movimentoRepository,
            $itemRepository,
            $dbTransaction
        );
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $this->sut->createMovimentacaoSaida($request);
    }
    public function test_createMovimentacaoSaida_withValidData_returnsCollection(): void
    {
        // Assert
        $user = User::factory()->makeOne(['id' => (string)Str::uuid()]);
        Auth::shouldReceive('user')->andReturn($user);
        $request = new MovimentoSaidaRequest();
        $itemId = (string)Items::factory()->makeOne(['id' => Str::uuid()])->id;
        $request->saidas = [
            [
                'itemId' => $itemId,
                'quantidade' => rand(2, 100),
                'data' => now()->format('Y-m-d'),
                'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                'localDestino' => $this->faker->locale(),
            ]
        ];
        $expectedItemResult = TestUtils::mockObj(ItemDto::class);
        $expectedItemResult->id = $itemId;
        $expectedCreateResult = Movimentos::factory()->makeOne(['id' => Str::uuid()]);
        $expectedCreateResult->item_id = $itemId;
        $expectedCreateResult->quantidade = $request->saidas[0]['quantidade'];
        $expectedCreateResult->data_movimentacao = $request->saidas[0]['data'];
        $expectedCreateResult->numero_controle_saida = $request->saidas[0]['numeroControleSaida'];
        $expectedCreateResult->local_destino = $request->saidas[0]['localDestino'];
        $expectedResult = Collection::times(1)->map(fn () => TestUtils::mockObj(MovimentoDto::class));
        $jwtToken = Mockery::mock(JwtToken::class, function (MockInterface $mock){
            $mock->shouldReceive('validateRole')
                ->once()
                ->with('Criar Movimentações');
        });
        /** @var JwtTokenProvider $jwtTokenProvider */
        $jwtTokenProvider = Mockery::mock(JwtTokenProvider::class, function (MockInterface $mock) use ($jwtToken) {
            $mock->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        });
        /** @var IItemRepository $itemRepository */
        $itemRepository = Mockery::mock(IItemRepository::class, function (MockInterface $mock) use ($expectedItemResult) {
            $mock->shouldReceive('getItemById')
                ->once()
                ->andReturn($expectedItemResult);
            $mock->shouldReceive('updateItem')
                ->once()
                ->andReturn(true);
        });
        /** @var IMovimentoRepository $movimentoRepository */
        $movimentoRepository = Mockery::mock(IMovimentoRepository::class, function (MockInterface $mock) use ($expectedCreateResult, $expectedResult) {
            $mock->shouldReceive('getMovimentoByNumeroControleSaida')
                ->once()
                ->andReturn(null);
            $mock->shouldReceive('createMovimentacao')
                ->once()
                ->andReturn($expectedCreateResult);
            $mock->shouldReceive('getMovimentacoesByIdList')
                ->once()
                ->andReturn($expectedResult);
        });
        $dbTransaction = new DbTransactionsTestUtil();
        $this->sut = new MovimentoSaidaService(
            $jwtTokenProvider,
            $movimentoRepository,
            $itemRepository,
            $dbTransaction
        );
        // Act
        $result = $this->sut->createMovimentacaoSaida($request);
        // Assert
        $this->assertEquals($expectedResult, $result);
        $this->assertInstanceOf(Collection::class, $result);
    }
}
