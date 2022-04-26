<?php

namespace EscolaLms\StationaryEvents\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListStationaryEventCurrentUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['string'],
        ];
    }
}
