<?php

namespace EscolaLms\StationaryEvents\Tests\Api;

use EscolaLms\StationaryEvents\Database\Seeders\StationaryEventSeeder;
use EscolaLms\StationaryEvents\Tests\TestCase;

class ExampleTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testExample(): void
    {
        $this->seed(StationaryEventSeeder::class);
        $this->assertTrue(true);
    }
}
