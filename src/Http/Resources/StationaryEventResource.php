<?php

namespace EscolaLms\StationaryEvents\Http\Resources;

use EscolaLms\Auth\Traits\ResourceExtandable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StationaryEventResource extends JsonResource
{
    use ResourceExtandable;

    public function toArray($request): array
    {
        $fields =  [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'short_desc' => $this->short_desc,
            'started_at' => $this->started_at,
            'finished_at' => $this->finished_at,
            'max_participants' => $this->max_participants,
            'place' => $this->place,
            'program' => $this->program,
            'categories' => $this->categories,
            'authors' => $this->authors ? UserResource::collection($this->authors) : [],
            'image_path' => $this->image_path,
            'image_url' => $this->image_path ? Storage::url($this->image_path) : null,
        ];

        return self::apply($fields, $this);
    }
}
