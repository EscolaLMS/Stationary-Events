<?php

namespace EscolaLms\StationaryEvents\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StationaryEventResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
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
    }
}
