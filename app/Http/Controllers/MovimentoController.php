<?php

namespace App\Http\Controllers;

use App\Support\Models\BaseResponse;
use App\Core\ApplicationModels\Pagination;
use Symfony\Component\HttpFoundation\Response;
use App\Core\Services\Movimento\IMovimentoListingService;
use App\Http\Requests\Movimento\MovimentosListingRequest;

class MovimentoController extends Controller
{
    public function __construct(
        private IMovimentoListingService $movimentoListingService
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
}
