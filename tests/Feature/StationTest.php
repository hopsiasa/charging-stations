<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Station;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StationTest extends TestCase
{
//    use RefreshDatabase;

    public function testIndex(): void
    {
        Station::factory(5)->create();

        $response = $this->get('/api/stations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'latitude',
                        'longitude',
                        'address',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        $station = Station::factory()->create();

        $response = $this->get("/api/stations/{$station->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'latitude',
                    'longitude',
                    'address',
                ],
            ]);
    }

    public function testStore(): void
    {
        $validData = [
            'name' => 'Test Station',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'company_id' => 4,
            'address' => 'Test Address',
        ];

        $response = $this->post('/api/stations', $validData);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                "id",
                "name",
                "latitude",
                "longitude",
                "address",
                "created_at",
                "updated_at",
            ],
        ]);

        $invalidData = [];

        $response = $this->post('/api/stations', $invalidData);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            "error"
        ]);
    }

    public function testUpdate(): void
    {
        $station = Station::factory()->create();

        $validUpdateData = [
            'name' => 'Test Station',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'company_id' => 4,
            'address' => 'Test Address',
        ];

        $validResponse = $this->json('PUT', '/api/stations/'.$station->id, $validUpdateData);

        $validResponse->assertStatus(200);

        $this->assertDatabaseHas('stations', $validUpdateData);

        $invalidUpdateData = [
            'name' => '',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'company_id' => 4,
            'address' => '',
        ];

        $invalidResponse = $this->json('PUT', '/api/stations/'.$station->id, $invalidUpdateData);

        $invalidResponse->assertStatus(422);

        $invalidResponse->assertJson(['error' => 'An error occurred while updating the station.']);

        $this->assertDatabaseHas('stations', $validUpdateData);
    }

    public function testDestroy(): void
    {
        $response = $this->delete("/api/stations/999");

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Station not found']);

        $station = Station::factory()->create();

        $response = $this->delete("/api/stations/{$station->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Station deleted successfully']);

        $response = $this->delete("/api/stations/{$station->id}");
    }

    public function testSearchStationsWithinRadius(): void
    {
        $testStation = Station::factory()->create([
            'name' => 'Test Station',
            'latitude' => '28.59603',
            'longitude' => '77.31449',
            'company_id' => 1,
            'address' => 'Test Address',
        ]);

        $latitude = '28.59603';
        $longitude = '77.31449';
        $radius = 5000;

        $response = $this->get("/api/stations/search?lat=$latitude&lon=$longitude&radius=$radius");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'stations' => [
                    '*' => [
                        'location' => [
                            'latitude',
                            'longitude',
                        ],
                        'companies' => [
                            '*' => [
                                'company_id',
                                'stations_in_company' => [
                                    '*' => [
                                        'id',
                                        'name',
                                        'company_id',
                                        'address',
                                        'distance',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->assertJsonFragment([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'company_id' => 1,
                'name' => 'Test Station',
                'address' => 'Test Address',
            ]);
    }
}
