<?php

namespace EscolaLms\StationaryEvents\Repositories;

use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\StationaryEvents\Models\StationaryEvent;
use EscolaLms\StationaryEvents\Repositories\Contracts\StationaryEventRepositoryContract;
use Illuminate\Database\Eloquent\Builder;

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
        return $this->allQueryBuilder($criteria)
            ->WhereRelation('users', 'user_id', '=', auth()->user()->getKey())
            ->orWhereRelation('authors', 'author_id', '=', auth()->user()->getKey());
    }
}
