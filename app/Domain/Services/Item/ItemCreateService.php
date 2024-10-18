<?php

namespace App\Domain\Services\Item;

use App\Models\Items;
use App\Core\Dtos\ItemDto;
use App\Http\Requests\Item\ItemCreateRequest;
use App\Core\Services\Item\IItemCreateService;
use App\Core\Repositories\Item\IItemRepository;
use App\Core\ApplicationModels\JwtTokenProvider;

class ItemCreateService implements IItemCreateService
{
    public function __construct(
        private IItemRepository $itemRepository,
        private JwtTokenProvider $jwtTokenProvider,
    )
    {
    }

    public function createItem(ItemCreateRequest $request): ItemDto
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Criar Items');
        $itemForCreate = $this->mapItemFromRequest($request);
        $itemCreated = $this->itemRepository->createItem($itemForCreate);
        return $this->itemRepository->getItemById($itemCreated->id);
    }
    private function mapItemFromRequest(ItemCreateRequest $request): Items
    {
        $item = new Items();
        $item->nome = $request->nome;
        $item->categoria = $request->categoria;
        $item->sub_categoria = $request->subCategoria;
        $item->descricao = $request->descricao;
        $item->estoque = $request->quantidade;
        $item->user_id = auth()->user()->id;
        return $item;
    }
}
