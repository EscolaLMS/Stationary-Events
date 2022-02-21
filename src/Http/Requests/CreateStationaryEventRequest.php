<?php

namespace EscolaLms\StationaryEvents\Http\Requests;

use EscolaLms\StationaryEvents\Models\StationaryEvent;
use EscolaLms\StationaryEvents\Rules\ValidAuthor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateStationaryEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', StationaryEvent::class);
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
            'program' => ['nullable', 'string', 'max:255'],
            'authors' => ['nullable', 'array'],
            'authors.*' => ['integer', new ValidAuthor()],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'string'],
        ];
    }
}
