<?php

namespace EscolaLms\StationaryEvents\Http\Requests;

use EscolaLms\StationaryEvents\Models\StationaryEvent;
use EscolaLms\StationaryEvents\Rules\ValidAuthor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Schema(
 *      schema="stationary-event-create-request",
 *      required={"name", "description", "started_at", "finished_at"},
 *      @OA\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="started_at",
 *          description="started_at",
 *          type="datetime"
 *      ),
 *      @OA\Property(
 *          property="finished_at",
 *          description="finished_at",
 *          type="datetime",
 *      ),
 *      @OA\Property(
 *          property="max_participants",
 *          description="max_participants",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="place",
 *          description="place",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="program",
 *          description="program",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="authors",
 *          description="authors",
 *          type="array",
 *          @OA\Items(
 *              type="integer",
 *          )
 *      ),
 *      @OA\Property(
 *          property="categories",
 *          description="categories",
 *          type="array",
 *          @OA\Items(
 *              type="integer",
 *          )
 *      ),
 * )
 *
 */
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
            'categories' => ['array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }
}
