<?php

namespace EscolaLms\StationaryEvents\Repositories;

use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\StationaryEvents\Models\StationaryEvent;
use EscolaLms\StationaryEvents\Models\StationaryEventAuthorPivot;
use EscolaLms\StationaryEvents\Models\StationaryEventUserPivot;
use EscolaLms\StationaryEvents\Repositories\Contracts\StationaryEventRepositoryContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StationaryEventRepository extends BaseRepository implements StationaryEventRepositoryContract
{
    protected $fieldSearchable = [
        'name',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StationaryEvent::class;
    }

    public function allQueryBuilder(array $criteria = []): Builder
    {
        $query = $this->allQuery();

        if (!empty($criteria)) {
            $query = $this->applyCriteria($query, $criteria);
        }

        return $query;
    }

    public function forCurrentUser(array $criteria = []): Builder
    {
        $userId = auth()->user()->getKey();

        return $this->allQueryBuilder($criteria)
            ->whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orWhereHas('authors', function ($query) use ($userId) {
                $query->where('author_id', $userId);
            })
            ->select(StationaryEvent::TABLE_NAME . '.*', DB::raw("
                (SELECT MAX(created_at) FROM (
                    SELECT user_pivot.created_at FROM " . StationaryEventUserPivot::TABLE_NAME . " AS user_pivot
                    WHERE user_pivot.stationary_event_id = " . StationaryEvent::TABLE_NAME . ".id AND user_pivot.user_id = $userId
                    UNION ALL
                    SELECT author_pivot.created_at FROM " . StationaryEventAuthorPivot::TABLE_NAME . " AS author_pivot
                    WHERE author_pivot.stationary_event_id = " . StationaryEvent::TABLE_NAME . ".id AND author_pivot.author_id = $userId
                ) AS x) AS max_created_at
            "))
            ->orderBy('max_created_at', 'desc');
    }
}
