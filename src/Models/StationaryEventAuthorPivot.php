<?php

namespace EscolaLms\StationaryEvents\Models;

use EscolaLms\Auth\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class StationaryEventAuthorPivot extends Pivot
{
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stationaryEvent(): BelongsTo
    {
        return $this->belongsTo(StationaryEvent::class);
    }
}
