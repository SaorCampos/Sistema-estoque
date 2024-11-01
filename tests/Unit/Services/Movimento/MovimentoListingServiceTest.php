<?php

namespace Tests\Unit\Services\Movimento;

use Mockery;
use Tests\TestCase;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\ApplicationModels\Pagination;
use App\Core\Dtos\MovimentoDto;
use App\Core\Repositories\Movimento\IMovimentoRepository;
use App\Core\Services\Movimento\IMovimentoListingService;
use App\Domain\Services\Movimento\MovimentoListingService;
use App\Http\Requests\Movimento\MovimentosListingRequest;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Tests\Utils\TestUtils;

class MovimentoListingServiceTest extends TestCase
{
    private IMovimentoListingService $sut;

    public function test_getAllMovimetacoes_returnsPaginatedList(): void
    {
        // Assert
        $jwtToken = Mockery::mock(JwtToken::class, function (MockInterface $mock){
            $mock->shouldReceive('validateRole')
                ->once()
                ->with('Listar Movimentações');
        });
        /** @var JwtTokenProvider $jwtTokenProvider */
        $jwtTokenProvider = Mockery::mock(JwtTokenProvider::class, function (MockInterface $mock) use ($jwtToken) {
            $mock->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        });
        $request = new MovimentosListingRequest();
        /** @var Pagination $pagination */
        $pagination = Mockery::mock(Pagination::class);
        $pagination->page = 1;
        $pagination->perPage = 100;
        $expectedResult = new PaginatedList(
            Collection::times(100)
            ->map(fn () => TestUtils::mockObj(MovimentoDto::class))->toArray(),
            TestUtils::mockObj(Pagination::class)
        );
        /** @var IMovimentoRepository $movimentoRepository */
        $movimentoRepository = Mockery::mock(IMovimentoRepository::class, function (MockInterface $mock) use ($request, $pagination, $expectedResult) {
            $mock->shouldReceive('getAllMovimetacoes')
                ->once()
                ->with($request, $pagination)
                ->andReturn($expectedResult);
        });
        $this->sut = new MovimentoListingService($movimentoRepository, $jwtTokenProvider);
        // Act
        $result = $this->sut->getAllMovimetacoes($request, $pagination);
        // Assert
        $this->assertInstanceOf(PaginatedList::class, $result);
        $this->assertEquals($expectedResult, $result);
    }
}
