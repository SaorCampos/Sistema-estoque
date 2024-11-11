<?php

namespace App\Http\Controllers;

use App\Support\Models\BaseResponse;
use App\Core\ApplicationModels\Pagination;
use App\Core\Services\Movimento\IMovimentoEntradaService;
use Symfony\Component\HttpFoundation\Response;
use App\Core\Services\Movimento\IMovimentoListingService;
use App\Core\Services\Movimento\IMovimentoSaidaService;
use App\Http\Requests\Movimento\MovimentoEntradaRequest;
use App\Http\Requests\Movimento\MovimentoSaidaRequest;
use App\Http\Requests\Movimento\MovimentosListingRequest;

class MovimentoController extends Controller
{
    public function __construct(
        private IMovimentoListingService $movimentoListingService,
        private IMovimentoEntradaService $movimentoEntradaService,
        private IMovimentoSaidaService $movimentoSaidaService,
    )
    {}

    public function getAllMovimetacoes(MovimentosListingRequest $request): Response
    {
        $list = $this->movimentoListingService->getAllMovimetacoes(
            request: $request,
            pagination: Pagination::createFromRequest($request)
        );
        return BaseResponse::builder()
            ->setData($list)
            ->setMessage('Movimentações listadas com sucesso!')
            ->response();
    }
    public function createMovimentacaoEntrada(MovimentoEntradaRequest $request): Response
    {
        $movimentacoes = $this->movimentoEntradaService->createMovimentacaoEntrada($request);
        return BaseResponse::builder()
            ->setData($movimentacoes)
            ->setMessage('Movimentações criada com sucesso!')
            ->response();
    }
    public function createMovimentacaoSaida(MovimentoSaidaRequest $request): Response
    {
        $movimentacoes = $this->movimentoSaidaService->createMovimentacaoSaida($request);
        return BaseResponse::builder()
            ->setData($movimentacoes)
            ->setMessage('Movimentações criada com sucesso!')
            ->response();
    }
}
