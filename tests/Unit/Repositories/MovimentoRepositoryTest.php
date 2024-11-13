<?php

namespace Tests\Unit\Repositories;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Items;
use App\Models\Movimentos;
use App\Core\Dtos\MovimentoDto;
use App\Core\ApplicationModels\Pagination;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Data\Repositories\Movimento\MovimentoRepository;
use App\Core\Repositories\Movimento\IMovimentoRepository;
use App\Http\Requests\Movimento\MovimentosListingRequest;
use Illuminate\Support\Collection;

class MovimentoRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private IMovimentoRepository $sut;

    public function test_getAllMovimetacoes_returnsPaginatedList(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        Movimentos::factory(100)->create();
        $request = new MovimentosListingRequest();
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
        }
    }
    public function test_getAllMovimetacoes_usingFilterTipoMovimentacaoAsEntrada_returnsPaginatedList(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        Movimentos::factory(100)->create();
        $request = new MovimentosListingRequest();
        $request->tipoMovimentacao = 'ENTRADA';
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
            $this->assertEquals($request->tipoMovimentacao, $item->tipoMovimentacao);
        }
    }
    public function test_getAllMovimetacoes_usingFilterTipoMovimentacaoAsSaida_returnsPaginatedList(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        Movimentos::factory(100)->create();
        $request = new MovimentosListingRequest();
        $request->tipoMovimentacao = 'SAIDA';
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
            $this->assertEquals($request->tipoMovimentacao, $item->tipoMovimentacao);
        }
    }
    public function test_getAllMovimetacoes_usingFilterNomeItem_returnsPaginatedList(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        Movimentos::factory(100)->create();
        $request = new MovimentosListingRequest();
        $item = Items::get()->first();
        $request->nomeItem = $item->nome;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
            $this->assertEquals($request->nomeItem, $item->nomeItem);
        }
    }
    public function test_getAllMovimetacoes_usingFilterItemId_returnsPaginatedList(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        Movimentos::factory(100)->create();
        $request = new MovimentosListingRequest();
        $item = Items::get()->first();
        $request->itemId = $item->id;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
            $this->assertEquals($request->itemId, $item->itemId);
        }
    }
    public function test_getAllMovimetacoes_usingFilterDataMovimentacao_returnsPaginatedList(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        Movimentos::factory(100)->create();
        $request = new MovimentosListingRequest();
        $movimento = Movimentos::get()->first();
        $request->dataMovimentacao = $movimento->data_movimentacao;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
            $this->assertEquals($request->dataMovimentacao, $item->dataMovimentacao);
        }
    }
    public function test_getAllMovimetacoes_usingFilterNotaFiscal_returnsPaginatedList(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        Movimentos::factory(100)->create();
        $request = new MovimentosListingRequest();
        $movimento = Movimentos::get()->first();
        $request->notaFiscal = $movimento->nota_fiscal;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
            $this->assertEquals($request->notaFiscal, $item->notaFiscal);
        }
    }
    public function test_getAllMovimetacoes_usingFilterFornecedor_returnsPaginatedList(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        Movimentos::factory(100)->create();
        $request = new MovimentosListingRequest();
        $movimento = Movimentos::get()->first();
        $request->fornecedor = $movimento->fornecedor;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
            $this->assertEquals($request->fornecedor, $item->fornecedor);
        }
    }
    public function test_getAllMovimetacoes_usingFilterLocalDestino_returnsPaginatedList(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        Movimentos::factory(100)->create();
        $request = new MovimentosListingRequest();
        $movimento = Movimentos::get()->first();
        $request->localDestino = $movimento->local_destino;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
            $this->assertEquals($request->localDestino, $item->localDestino);
        }
    }
    public function test_getAllMovimetacoes_usingFilterUsuarioResponsavel_returnsPaginatedList(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        Movimentos::factory(100)->create();
        $request = new MovimentosListingRequest();
        $movimento = Movimentos::factory()->createOne();
        $usuario = User::where('id', '=', $movimento->user_id)->first();
        $request->usuarioResponsavel = $usuario->name;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
            $this->assertEquals($request->usuarioResponsavel, $item->usuarioResponsavel);
        }
    }
    public function test_getAllMovimetacoes_usingFilterDataInicialAndDataFinal_returnsPaginatedList(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        Movimentos::factory(100)->create();
        $request = new MovimentosListingRequest();
        $movimento = Movimentos::factory()->createOne();
        $dataInicial = Carbon::parse($movimento->data_movimentacao)->subDays(1)->format('Y-m-d');
        $dataFinal = Carbon::parse($movimento->data_movimentacao)->addDays(1)->format('Y-m-d');
        $request->dataInicial = $dataInicial;
        $request->dataFinal = $dataFinal;
        $pagination = new Pagination();
        $pagination->page = 1;
        $pagination->perPage = 10;
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertEquals($pagination->page, $result->pagination->page);
        $this->assertEquals($pagination->perPage, $result->pagination->perPage);
        $this->assertIsArray($result->list);
        foreach ($result->list as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
            $this->assertGreaterThanOrEqual($request->dataInicial, $item->dataMovimentacao);
            $this->assertLessThanOrEqual($dataFinal . ' 23:59:59', $item->dataMovimentacao);
        }
    }
    public function test_createMovimentacao_withValidData_returnsMovimentoModel(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        $movimento = Movimentos::factory()->makeOne();
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->createMovimentacao($movimento);
        // Assert
        $this->assertInstanceOf(Movimentos::class, $result);
        $this->assertEquals($movimento->tipo_movimentacao, $result->tipo_movimentacao);
        $this->assertEquals($movimento->item_id, $result->item_id);
        $this->assertEquals($movimento->quantidade, $result->quantidade);
        $this->assertEquals($movimento->data_movimentacao, $result->data_movimentacao);
        $this->assertEquals($movimento->nota_fiscal, $result->nota_fiscal);
        $this->assertEquals($movimento->fornecedor, $result->fornecedor);
        $this->assertEquals($movimento->local_destino, $result->local_destino);
        $this->assertEquals($movimento->user_id, $result->user_id);
    }
    public function test_getMovimentoById_onValidData_returnsMovimentoDto(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        $movimento = Movimentos::factory()->createOne();
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getMovimentoById($movimento->id);
        // Assert
        $this->assertInstanceOf(MovimentoDto::class, $result);
        $this->assertEquals($movimento->id, $result->movimentacaoId);
    }
    public function test_getMovimentacoesByIdList_returnsCollection(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        $movimentos = Movimentos::factory(10)->create();
        $ids = $movimentos->pluck('id')->toArray();
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getMovimentacoesByIdList($ids);
        // Assert
        $this->assertInstanceOf(Collection::class,$result);
        foreach ($result as $item) {
            $this->assertInstanceOf(MovimentoDto::class, $item);
            $this->assertContains($item->movimentacaoId, $ids);
        }
    }
    public function test_getMovimentacaoByNotaFiscal_onValidData_returnsMovimentoDto(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        $movimento = Movimentos::factory()->createOne();
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getMovimentacaoByNotaFiscal($movimento->nota_fiscal);
        // Assert
        $this->assertInstanceOf(MovimentoDto::class, $result);
        $this->assertEquals($movimento->nota_fiscal, $result->notaFiscal);
    }
    public function test_getMovimentacaoByNotaFiscal_onInvalidData_returnsNull(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getMovimentacaoByNotaFiscal(rand(1, 1000));
        // Assert
        $this->assertNull($result);
    }
    public function test_getMovimentoByNumeroControleSaida_onValidData_returnsMovimentoDto(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        $movimento = Movimentos::factory()->createOne();
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getMovimentoByNumeroControleSaida($movimento->numero_controle_saida);
        // Assert
        $this->assertInstanceOf(MovimentoDto::class, $result);
        $this->assertEquals($movimento->numero_controle_saida, $result->numeroControleSaida);
    }
    public function test_getMovimentoByNumeroControleSaida_onInvalidData_returnsNull(): void
    {
        // Arrange
        Movimentos::truncate();
        User::truncate();
        $this->sut = new MovimentoRepository();
        // Act
        $result = $this->sut->getMovimentoByNumeroControleSaida(rand(1, 1000));
        // Assert
        $this->assertNull($result);
    }
}
