<?php

namespace EscolaLms\StationaryEvents\Services;

use EscolaLms\Auth\Models\User;
use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Repositories\Criteria\Primitives\DateCriterion;
use EscolaLms\Core\Repositories\Criteria\Primitives\LikeCriterion;
use EscolaLms\Courses\Repositories\Criteria\Primitives\OrderCriterion;
use EscolaLms\StationaryEvents\Events\StationaryEventAssigned;
use EscolaLms\StationaryEvents\Events\StationaryEventAuthorAssigned;
use EscolaLms\StationaryEvents\Events\StationaryEventAuthorUnassigned;
use EscolaLms\StationaryEvents\Events\StationaryEventUnassigned;
use EscolaLms\StationaryEvents\Models\StationaryEvent;
use EscolaLms\StationaryEvents\Repositories\Contracts\StationaryEventRepositoryContract;
use EscolaLms\StationaryEvents\Services\Contracts\StationaryEventServiceContract;
use Illuminate\Database\Eloquent\Builder;

class StationaryEventService implements StationaryEventServiceContract
{
    private StationaryEventRepositoryContract $stationaryEventRepository;

    public function __construct(StationaryEventRepositoryContract $stationaryEventRepository)
    {
        $this->stationaryEventRepository = $stationaryEventRepository;
    }

    public function getStationaryEventList(OrderDto $orderDto, array $search = [], bool $onlyActive = false): Builder
    {
        $criteria = [];

        if (!is_null($orderDto->getOrder())) {
            $criteria[] = new OrderCriterion($orderDto->getOrderBy(), $orderDto->getOrder());
        }

        if (isset($search['name'])) {
            $criteria[] = new LikeCriterion('name', $search['name']);
            unset($search['name']);
        }

        if ($onlyActive) {
            $criteria[] = new DateCriterion('started_at', now()->format('Y-m-d H:i:s'), '>=');
        }

        return $this->stationaryEventRepository->allQueryBuilder(
            $search,
            $criteria
        )->with(['authors']);
    }


    public function create(array $data): StationaryEvent
    {
        $stationaryEvent = $this->stationaryEventRepository->create($data);
        $this->syncAuthors($stationaryEvent, $data['authors'] ?? []);
        $this->saveTags($stationaryEvent, $data['tags'] ?? []);
        return $stationaryEvent;
    }

    public function update(StationaryEvent $stationaryEvent, array $data): StationaryEvent
    {
        $stationaryEvent = $this->stationaryEventRepository->update($data, $stationaryEvent->getKey());
        $this->syncAuthors($stationaryEvent, $data['authors'] ?? []);
        $this->saveTags($stationaryEvent, $data['tags'] ?? []);

        return $stationaryEvent;
    }

    public function delete(StationaryEvent $stationaryEvent): ?bool
    {
        return $this->stationaryEventRepository->delete($stationaryEvent->getKey());
    }

    public function addAccessForUsers(StationaryEvent $stationaryEvent, array $users = []): void
    {
        $result = $stationaryEvent->users()->syncWithoutDetaching($users);
        $this->dispatchEventForUsersAttachedToStationaryEvent($stationaryEvent, $result['attached']);
    }

    private function syncAuthors(StationaryEvent $stationaryEvent, array $authors = []): void
    {
        $syncResult = $stationaryEvent->authors()->sync($authors);

        foreach ($syncResult['attached'] as $attached) {
            event(new StationaryEventAuthorAssigned(User::find($attached), $stationaryEvent));
        }

        foreach ($syncResult['detached'] as $attached) {
            event(new StationaryEventAuthorUnassigned(User::find($attached), $stationaryEvent));
        }

    }

    private function dispatchEventForUsersAttachedToStationaryEvent(StationaryEvent $stationaryEvent, array $users = []): void
    {
        foreach ($users as $attached) {
            $user = is_int($attached) ? User::find($attached) : $attached;
            event(new StationaryEventAssigned($user, $stationaryEvent));
        }
    }

    private function dispatchEventForUsersDetachedToStationaryEvent(StationaryEvent $stationaryEvent, array $users = []): void
    {
        foreach ($users as $detached) {
            $user = is_int($detached) ? User::find($detached) : $detached;
            event(new StationaryEventUnassigned($user, $stationaryEvent));
        }
    }

    private function saveTags(StationaryEvent $stationaryEvent, array $tagArray = []): void
    {
        $stationaryEvent->tags()->delete();

        $tags = array_map(function ($tag) {
            return ['title' => $tag];
        }, $tagArray);

        $stationaryEvent->tags()->createMany($tags);
    }
}
