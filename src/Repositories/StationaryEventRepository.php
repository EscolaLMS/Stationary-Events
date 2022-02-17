<?php

namespace EscolaLms\StationaryEvents\Repositories;

use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\StationaryEvents\Models\StationaryEvent;
use EscolaLms\StationaryEvents\Repositories\Contracts\StationaryEventRepositoryContract;

class StationaryEventRepository extends BaseRepository implements  StationaryEventRepositoryContract
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
}
