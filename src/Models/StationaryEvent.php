<?php

namespace EscolaLms\StationaryEvents\Models;

use EscolaLms\Auth\Models\User;
use EscolaLms\Categories\Models\Category;
use EscolaLms\StationaryEvents\Database\Factories\StationaryEventFactory;
use EscolaLms\StationaryEvents\Enum\StationaryEventStatusEnum;
use EscolaLms\StationaryEvents\Events\ImageChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @OA\Schema(
 *      schema="stationary-event",
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
 *          property="short_desc",
 *          description="short description",
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
 *          property="image_path",
 *          description="image_path",
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
 *      @OA\Property(
 *          property="status",
 *          description="status",
 *          type="string"
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
        'short_desc',
        'started_at',
        'finished_at',
        'max_participants',
        'place',
        'program',
        'image_path',
        'status',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'stationary_event_users', 'stationary_event_id', 'user_id')->using(StationaryEventUserPivot::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'stationary_event_authors', 'stationary_event_id', 'author_id')->using(StationaryEventAuthorPivot::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function isPublished(): bool
    {
        return in_array($this->status, [
            StationaryEventStatusEnum::PUBLISHED_UNACTIVATED,
            StationaryEventStatusEnum::PUBLISHED
        ]);
    }

    protected static function newFactory(): StationaryEventFactory
    {
        return StationaryEventFactory::new();
    }

    protected static function booted()
    {
        self::updated(function (StationaryEvent $stationaryEvent) {
            if ($stationaryEvent->wasChanged('image_path') && is_string($stationaryEvent->getOriginal('image_path'))) {
                event(new ImageChanged($stationaryEvent->getOriginal('image_path')));
            }
        });
    }
}
