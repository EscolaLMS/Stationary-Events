<?php

namespace EscolaLms\StationaryEvents\Tests\Api;

use EscolaLms\Categories\Models\Category;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\StationaryEvents\Database\Seeders\StationaryEventPermissionSeeder;
use EscolaLms\StationaryEvents\Events\StationaryEventAuthorAssigned;
use EscolaLms\StationaryEvents\Models\StationaryEvent;
use EscolaLms\StationaryEvents\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

class StationaryEventCreateApiTest extends TestCase
{
    use DatabaseTransactions, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(StationaryEventPermissionSeeder::class);
        $this->user = $this->makeInstructor();
    }

    public function testStationaryEventCreateUnauthorized(): void
    {
        $this->postJson('api/admin/stationary-events')->assertUnauthorized();
    }

    public function testStationaryEventCreateRequiredValidation(): void
    {
        $response = $this->actingAs($this->user, 'api')->postJson('api/admin/stationary-events');
        $response->assertJsonValidationErrors(['name', 'started_at', 'finished_at', 'description']);
    }

    public function testStationaryEventCreateAuthorMustByTutor(): void
    {
        $stationaryEvent = StationaryEvent::factory()->make()->toArray();
        $student = $this->makeStudent();
        $stationaryEvent['authors'] = [$student->getKey()];

        $response = $this->actingAs($this->user, 'api')->postJson(
            'api/admin/stationary-events',
            $stationaryEvent
        );

        $response->assertJsonValidationErrors(['authors.0']);
    }

    public function testStationaryEventCreate(): void
    {
        $stationaryEvent = StationaryEvent::factory()->make()->toArray();

        $this->response = $this->actingAs($this->user, 'api')->postJson(
            'api/admin/stationary-events',
            $stationaryEvent
        )->assertCreated();

        $this->assertApiResponse($stationaryEvent);
    }

    public function testStationaryEventCreateWithAuthor(): void
    {
        Event::fake([StationaryEventAuthorAssigned::class]);
        $stationaryEvent = StationaryEvent::factory()->make()->toArray();

        $tutor = $this->makeInstructor();

        $stationaryEvent['authors'] = [$tutor->getKey()];

        $this->response = $this->actingAs($this->user, 'api')->postJson(
            'api/admin/stationary-events',
            $stationaryEvent
        )->assertCreated();

        $this->response->assertJsonFragment([
            'authors' => [
                [
                    'id' => $tutor->getKey(),
                    'first_name' => $tutor->first_name,
                    'last_name' => $tutor->last_name,
                    'email' => $tutor->email,
                    'path_avatar' => $tutor->path_avatar,
                ],
            ],
        ]);

        Event::assertDispatched(StationaryEventAuthorAssigned::class);
    }

    public function testStationaryEventCreateWithCategories(): void
    {
        $categories = Category::factory()->count(2)->create()->pluck('id')->toArray();
        $stationaryEvent = StationaryEvent::factory()->make()->toArray();
        $stationaryEvent['categories'] = $categories;

        $this->response = $this->actingAs($this->user, 'api')->postJson(
            'api/admin/stationary-events',
            $stationaryEvent
        )->assertCreated();

        $this->response->assertJsonFragment([
            'name' => $stationaryEvent['name'],
            'description' => $stationaryEvent['description'],
            'started_at' => $stationaryEvent['started_at'],
            'finished_at' => $stationaryEvent['finished_at'],
            'place' => $stationaryEvent['place'],
            'max_participants' => $stationaryEvent['max_participants'],
        ]);
        $this->response->assertJsonCount(2, 'data.categories');
        $this->response->assertJson(fn(AssertableJson $json) => $json->has(
            'data', fn($json) => $json->has(
                'categories', fn(AssertableJson $json) => $json->each(
                    fn(AssertableJson $json) => $json->where('id', fn($json) => in_array($json, $categories))->etc()
                )->etc()
            )->etc()
        )->etc());
    }
}
