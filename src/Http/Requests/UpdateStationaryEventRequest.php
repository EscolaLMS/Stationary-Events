<?php

namespace EscolaLms\StationaryEvents\Http\Requests;

use EscolaLms\StationaryEvents\Models\StationaryEvent;
use Illuminate\Support\Facades\Gate;

class UpdateStationaryEventRequest extends CreateStationaryEventRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->getStationaryEvent());
    }

    public function getStationaryEvent(): StationaryEvent
    {
        return StationaryEvent::findOrFail($this->route('id'));
    }
}
