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
            'id' => $this->resource->id,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'short_desc' => $this->resource->short_desc,
            'started_at' => $this->resource->started_at,
            'finished_at' => $this->resource->finished_at,
            'max_participants' => $this->resource->max_participants,
            'place' => $this->resource->place,
            'program' => $this->resource->program,
            'categories' => $this->resource->categories,
            'status' => $this->resource->status,
            'authors' => $this->resource->authors ? UserResource::collection($this->resource->authors) : [],
            'image_path' => $this->resource->image_path,
            'image_url' => $this->resource->image_path ? Storage::url($this->resource->image_path) : null,
            'in_coming' => $this->resource->in_coming,
            'is_ended' => $this->resource->is_ended,
            'is_started' => $this->resource->is_started,
            'agenda' => $this->resource->agenda,
        ];

        return self::apply($fields, $this);
    }
}
