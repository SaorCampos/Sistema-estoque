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
use App\Http\Requests\Movimento\MovimentoEntradaRequest;
use App\Core\Repositories\Movimento\IMovimentoRepository;
use App\Core\Services\Movimento\IMovimentoEntradaService;
use App\Domain\Services\Movimento\MovimentoEntradaService;

class MovimentoEntradaServiceTest extends TestCase
{
    use WithFaker;
    private IMovimentoEntradaService $sut;

    public function test_createMovimentacaoEntrada_withInvalidItemId__returnsException(): void
    {
        // Assert
        $request = new MovimentoEntradaRequest();
        $request->entradas = [
            [
                'itemId' => (string)Str::uuid(),
                'quantidade' => rand(1, 10000),
                'data' => now()->format('Y-m-d'),
                'notaFiscal' => rand(1, 10000),
                'fornecedor' => $this->faker()->company(),
            ],
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
        $movimentoRepository = Mockery::mock(IMovimentoRepository::class, function (MockInterface $mock){
            $mock->shouldReceive('getMovimentacaoByNotaFiscal')
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
        $this->sut = new MovimentoEntradaService(
            $jwtTokenProvider,
            $movimentoRepository,
            $itemRepository,
            $dbTransaction
        );
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $this->sut->createMovimentacaoEntrada($request);
    }
    public function test_createMovimentacaoEntrada_withValidData_returnsCollection(): void
    {
        // Assert
        $request = new MovimentoEntradaRequest();
        $itemId = (string)Items::factory()->makeOne(['id' => Str::uuid()])->id;
        $request->entradas = [
            [
                'itemId' => $itemId,
                'quantidade' => rand(1, 10000),
                'data' => now()->format('Y-m-d'),
                'notaFiscal' => rand(1, 10000),
                'fornecedor' => $this->faker()->company(),
            ],
        ];
        $user = User::factory()->makeOne(['id' => (string)Str::uuid()]);
        Auth::shouldReceive('user')->andReturn($user);
        $expectedItemResult = TestUtils::mockObj(ItemDto::class);
        $expectedItemResult->id = $itemId;
        $expectedCreateResult = Movimentos::factory()->makeOne(['id' => Str::uuid()]);
        $expectedCreateResult->item_id = $itemId;
        $expectedCreateResult->quantidade = $request->entradas[0]['quantidade'];
        $expectedCreateResult->data_movimentacao = $request->entradas[0]['data'];
        $expectedCreateResult->nota_fiscal = $request->entradas[0]['notaFiscal'];
        $expectedCreateResult->fornecedor = $request->entradas[0]['fornecedor'];
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
            $mock->shouldReceive('getMovimentacaoByNotaFiscal')
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
        $this->sut = new MovimentoEntradaService(
            $jwtTokenProvider,
            $movimentoRepository,
            $itemRepository,
            $dbTransaction
        );
        // Act
        $result = $this->sut->createMovimentacaoEntrada($request);
        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals($expectedResult, $result);
    }
}
