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

    public function allQueryBuilder(array $search = [], array $criteria = []): Builder
    {
        $query = $this->allQuery($search);

        if (!empty($criteria)) {
            $query = $this->applyCriteria($query, $criteria);
        }

        return $query;
    }
}
