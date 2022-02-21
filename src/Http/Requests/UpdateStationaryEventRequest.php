<?php

namespace EscolaLms\StationaryEvents\Http\Requests;

use EscolaLms\StationaryEvents\Models\StationaryEvent;
use EscolaLms\StationaryEvents\Rules\ValidAuthor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateStationaryEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->getStationaryEvent());
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'started_at' => ['required', 'date', 'after:now'],
            'finished_at' => ['required', 'date', 'after:started_at'],
            'base_price' => ['nullable', 'integer', 'min:0'],
            'max_participants' => ['nullable', 'integer', 'min:0'],
            'place' => ['nullable', 'string', 'max:255'],
            'program' => ['nullable', 'string'],
            'authors.*' => ['integer', new ValidAuthor()],
        ];
    }

    public function getStationaryEvent(): StationaryEvent
    {
        return StationaryEvent::findOrFail($this->route('id'));
    }
}
