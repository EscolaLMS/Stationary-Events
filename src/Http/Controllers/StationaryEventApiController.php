<?php

namespace EscolaLms\StationaryEvents\Http\Controllers;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\StationaryEvents\Http\Controllers\Swagger\StationaryEventApiSwagger;
use EscolaLms\StationaryEvents\Http\Requests\ReadStationaryEventPublicRequest;
use EscolaLms\StationaryEvents\Http\Resources\StationaryEventResource;
use EscolaLms\StationaryEvents\Services\Contracts\StationaryEventServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StationaryEventApiController extends EscolaLmsBaseController implements StationaryEventApiSwagger
{
    private StationaryEventServiceContract $stationaryEventService;

    public function __construct(StationaryEventServiceContract $stationaryEventService)
    {
        $this->stationaryEventService = $stationaryEventService;
    }

    public function index(Request $request): JsonResponse
    {
        $search = $request->except(['limit', 'skip', 'order', 'order_by']);
        $orderDto = OrderDto::instantiateFromRequest($request);

        $stationaryEvents = $this->stationaryEventService
            ->getStationaryEventList($orderDto, $search, true)
            ->paginate($request->get('per_page') ?? 15);

        return $this->sendResponseForResource(
            StationaryEventResource::collection($stationaryEvents),
            __('Stationary events retrieved successfully')
        );
    }

    public function show(ReadStationaryEventPublicRequest $request): JsonResponse
    {
        return $this->sendResponseForResource(StationaryEventResource::make($request->getStationaryEvent()));
    }
}
