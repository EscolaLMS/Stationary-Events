<?php

namespace EscolaLms\StationaryEvents\Models;

use EscolaLms\Auth\Models\User;
use EscolaLms\StationaryEvents\Database\Factories\StationaryEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @OA\Schema(
 *      schema="Stationary event",
 *      required={"name", "description", "started_at", "finished_at"},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *      ),
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
 *          property="base_price",
 *          description="base_price",
 *          type="integer"
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
 *          property="programm",
 *          description="programm",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="datetime",
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="datetime",
 *      ),
 * )
 *
 */
class StationaryEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'started_at',
        'finished_at',
        'base_price',
        'max_participants',
        'place',
        'program',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'stationary_event_users', 'stationary_event_id', 'user_id')->using(StationaryEventUserPivot::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'stationary_event_authors', 'stationary_event_id', 'author_id')->using(StationaryEventAuthorPivot::class);
    }

    protected static function newFactory(): StationaryEventFactory
    {
        return StationaryEventFactory::new();
    }
}
