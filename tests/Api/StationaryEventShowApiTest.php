<?php

namespace EscolaLms\StationaryEvents\Tests\Api;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\StationaryEvents\Database\Seeders\StationaryEventPermissionSeeder;
use EscolaLms\StationaryEvents\Models\StationaryEvent;
use EscolaLms\StationaryEvents\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StationaryEventShowApiTest extends TestCase
{
    use DatabaseTransactions, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(StationaryEventPermissionSeeder::class);
        $this->user = $this->makeInstructor();
        $this->stationaryEvent = StationaryEvent::factory()->create();
        $this->stationaryEvent->authors()->sync($this->user->getKey());
    }

    public function testStationaryEventShowUnauthorized(): void
    {
        $this->getJson('api/admin/stationary-events/' . $this->stationaryEvent->getKey())
            ->assertUnauthorized();
    }

    public function testStationaryEventShow(): void
    {
        $this->response = $this->actingAs($this->user, 'api')
            ->getJson('api/admin/stationary-events/' . $this->stationaryEvent->getKey())
            ->assertOk();

        $this->assertApiResponse($this->stationaryEvent->toArray());

        $this->response->assertJsonFragment([
            'authors' => [
                [
                    'id' => $this->user->getKey(),
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                    'email' => $this->user->email,
                    'path_avatar' => $this->user->path_avatar,
                ],
            ],
        ]);
    }

    public function testStationaryEventPublicShow(): void
    {
        $this->response = $this->getJson('api/stationary-events/' . $this->stationaryEvent->getKey())
            ->assertOk();

        $this->assertApiResponse($this->stationaryEvent->toArray());

        $this->response->assertJsonFragment([
            'authors' => [
                [
                    'id' => $this->user->getKey(),
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
                    'email' => $this->user->email,
                    'path_avatar' => $this->user->path_avatar,
                ],
            ],
        ]);

        $this->response->assertJsonMissing([
            'users' => []
        ]);
    }
}
