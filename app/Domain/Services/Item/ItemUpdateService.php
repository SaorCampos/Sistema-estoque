<?php

namespace App\Domain\Services\Item;

use App\Core\Dtos\ItemDto;
use App\Http\Requests\Item\ItemUpdateRequest;
use App\Core\Services\Item\IItemUpdateService;
use App\Core\Repositories\Item\IItemRepository;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Models\Items;
use Illuminate\Http\Exceptions\HttpResponseException;

class ItemUpdateService implements IItemUpdateService
{
    public function __construct(
        private IItemRepository $itemRepository,
        private JwtTokenProvider $jwtTokenProvider,
    )
    {
    }

    public function updateItem(ItemUpdateRequest $request): ItemDto
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Editar Items');
        $itemDtoForUpdate = $this->itemRepository->getItemById($request->id);
        if (!$itemDtoForUpdate) {
            throw new HttpResponseException(response()->json(['message' => 'Item não encotrado.'], 404));
        }
        $item = $this->mapItemUpdateRequestToItem($request);
        $bool = $this->itemRepository->updateItem($itemDtoForUpdate->id, $item);
        if (!$bool) {
            throw new HttpResponseException(response()->json(['message' => 'Erro ao atualizar item.'], 500));
        }
        return $this->itemRepository->getItemById($itemDtoForUpdate->id);
    }
    private function mapItemUpdateRequestToItem(ItemUpdateRequest $request): Items
    {
        $item = new Items();
        $item->categoria = $request->categoria;
        $item->sub_categoria = $request->subCategoria;
        $item->descricao = $request->descricao;
        return $item;
    }
}
