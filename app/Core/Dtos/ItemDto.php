<?php
namespace App\Core\Dtos;
use App\Core\Traits\AutoMapper;
use App\Core\Traits\ArraySerializer;
use App\Core\Traits\EntityDefaultFields;
use App\Core\ApplicationModels\IArraySerializer;

class ItemDto implements IArraySerializer
{
    use ArraySerializer, AutoMapper, EntityDefaultFields;

    public string $nome;
    public int $quantidadeEstoque;
    public ?string $descricao;
    public ?string $categoria;
    public ?string $subCategoria;
}
