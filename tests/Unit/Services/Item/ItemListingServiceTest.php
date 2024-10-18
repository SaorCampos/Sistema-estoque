<?php

namespace Tests\Unit\Services\Item;

use Tests\TestCase;
use Mockery as Mock;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\Repositories\Item\IItemRepository;
use App\Http\Requests\Item\ItemsListingRequest;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Domain\Services\Item\ItemListingService;

class ItemListingServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_getAllItems_returnPaginatedList(): void
    {
        // Arrange
        $itemRepository = Mock::mock(IItemRepository::class);
        $pagination = Mock::mock(Pagination::class);
        $expectedResult = Mock::mock(PaginatedList::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $request = new ItemsListingRequest();
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Listar Items')
            ->once();
        $itemRepository->shouldReceive('getAllItems')
            ->once()
            ->andReturn($expectedResult);
        $itemListingService = new ItemListingService($itemRepository, $jwtTokenProvider);
        // Act
        $result = $itemListingService->getAllItems($request, $pagination);
        // Assert
        $this->assertEquals($expectedResult, $result);
    }
}
