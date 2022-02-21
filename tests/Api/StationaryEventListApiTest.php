<?php

namespace EscolaLms\StationaryEvents\Tests\Api;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\StationaryEvents\Database\Seeders\StationaryEventPermissionSeeder;
use EscolaLms\StationaryEvents\Models\StationaryEvent;
use EscolaLms\StationaryEvents\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StationaryEventListApiTest extends TestCase
{
    use DatabaseTransactions, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(StationaryEventPermissionSeeder::class);
        $this->user = $this->makeInstructor();
    }

    public function testAdminStationaryEventListUnauthorized(): void
    {
        $this->getJson('api/admin/stationary-events')->assertUnauthorized();
    }

    public function testStationaryEventsListWithFilter(): void
    {
        $stationaryEvent = StationaryEvent::factory()->create();
        $student = $this->makeStudent();
        $stationaryEvent->users()->sync($student);
        $author = $this->makeInstructor();
        $stationaryEvent->authors()->sync($author);

        $this->response = $this->actingAs($this->user, 'api')
            ->getJson('api/admin/stationary-events?name=' . $stationaryEvent->name)
            ->assertOk()
            ->assertJsonFragment([
                'id' => $stationaryEvent->getKey(),
                'name' => $stationaryEvent->name,
            ]);

        $this->response->assertJsonFragment([
            'authors' => [
                [
                    'id' => $author->getKey(),
                    'first_name' => $author->first_name,
                    'last_name' => $author->last_name,
                    'email' => $author->email,
                    'path_avatar' => $author->path_avatar,
                ],
            ],
            'users' => [
                [
                    'id' => $student->getKey(),
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'email' => $student->email,
                    'path_avatar' => $student->path_avatar,
                ],
            ]
        ]);
    }

    public function testStationaryEventsPublicListWithFilter(): void
    {
        $stationaryEvent = StationaryEvent::factory()->create();
        $stationaryEvent2 = StationaryEvent::factory(['started_at' => now()->modify('-1 days')])->create();

        $student = $this->makeStudent();
        $stationaryEvent->users()->sync($student);
        $author = $this->makeInstructor();
        $stationaryEvent->authors()->sync($author);

        $this->response = $this->getJson('api/stationary-events?name=' . $stationaryEvent->name)
            ->assertOk()
            ->assertJsonFragment([
                'id' => $stationaryEvent->getKey(),
                'name' => $stationaryEvent->name,
            ])
            ->assertJsonMissing([
                'id' => $stationaryEvent2->getKey(),
                'name' => $stationaryEvent2->name,
            ])
            ->assertJsonFragment([
                'authors' => [
                    [
                        'id' => $author->getKey(),
                        'first_name' => $author->first_name,
                        'last_name' => $author->last_name,
                        'email' => $author->email,
                        'path_avatar' => $author->path_avatar,
                    ],
                ],
            ])->assertJsonMissing([
                'users' => [
                    [
                        'id' => $student->getKey(),
                        'first_name' => $student->first_name,
                        'last_name' => $student->last_name,
                        'email' => $student->email,
                        'path_avatar' => $student->path_avatar,
                    ],
                ]
            ]);
    }
}
