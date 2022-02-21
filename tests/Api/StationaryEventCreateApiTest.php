<?php

namespace EscolaLms\StationaryEvents\Tests\Api;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\StationaryEvents\Database\Seeders\StationaryEventPermissionSeeder;
use EscolaLms\StationaryEvents\Events\StationaryEventAuthorAssigned;
use EscolaLms\StationaryEvents\Models\StationaryEvent;
use EscolaLms\StationaryEvents\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;

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

    public function testStationaryEventCreateWithTags(): void
    {
        $stationaryEvent = StationaryEvent::factory()->make()->toArray();
        $stationaryEvent['tags'] = ['Stationary', 'Event', 'Tags'];

        $this->response = $this->actingAs($this->user, 'api')->postJson(
            'api/admin/stationary-events',
            $stationaryEvent
        )->assertCreated();

        foreach ($this->response->getData()->data->tags as $tag) {
            $this->assertTrue(in_array($tag->title, $stationaryEvent['tags']));
        }
    }
}
