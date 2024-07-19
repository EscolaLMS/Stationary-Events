<?php

namespace EscolaLms\StationaryEvents\Repositories\Contracts;

use EscolaLms\Core\Repositories\Contracts\BaseRepositoryContract;
use Illuminate\Database\Eloquent\Builder;

interface StationaryEventRepositoryContract extends BaseRepositoryContract
{
    public function forCurrentUser(array $criteria = []): Builder;
    public function allQueryBuilder(array $criteria = []): Builder;
}
