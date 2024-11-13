<?php

namespace App\Core\Dtos;

use App\Core\Traits\AutoMapper;
use App\Core\Traits\ArraySerializer;
use App\Core\ApplicationModels\IArraySerializer;

class MovimentoDto implements IArraySerializer
{
    use ArraySerializer, AutoMapper;

    public string $movimentacaoId;
    public string $itemId;
    public string $nomeItem;
    public int $quantidadeEstoque;
    public string $tipoMovimentacao;
    public int $quantidadeMovimentada;
    public string $dataMovimentacao;
    public ?int $notaFiscal;
    public ?string $fornecedor;
    public ?int $numeroControleSaida;
    public ?string $localDestino;
    public string $usuarioResponsavel;
    public ?string $criadoEm;
    public ?string $atualizadoEm;
    public ?string $atualizadoPor;
}
