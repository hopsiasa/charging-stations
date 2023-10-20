<?php

namespace Tests\Feature;

use App\Models\Company;
use Tests\TestCase;

class CompanyTest extends TestCase
{
//    use RefreshDatabase;

    public function testIndex(): void
    {
        Company::factory(5)->create();

        $response = $this->get('/api/companies');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'parent_company_id',
                        'name',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        $company = Company::factory()->create();

        $response = $this->get("/api/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'parent_company_id',
                    'name',
                ],
            ]);
    }

    public function testStore(): void
    {
        $validData = [
            'parent_company_id' => 1,
            'name' => 'Some company name',
        ];

        $response = $this->post('/api/companies', $validData);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'parent_company_id'
            ],
        ]);

        $invalidData = [];

        $response = $this->post('/api/companies', $invalidData);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            "error"
        ]);
    }

    public function testUpdate(): void
    {
        $company = Company::factory()->create();

        $validUpdateData = [
            'parent_company_id' => 2,
            'name' => 'Updated Company Name',
        ];

        $validResponse = $this->json('PUT', '/api/companies/'.$company->id, $validUpdateData);

        $validResponse->assertStatus(200);

        $this->assertDatabaseHas('companies', $validUpdateData);

        $invalidUpdateData = [
            'parent_company_id' => 2,
        ];

        $invalidResponse = $this->json('PUT', '/api/companies/'.$company->id, $invalidUpdateData);

        $invalidResponse->assertStatus(422);

        $invalidResponse->assertJson(['error' => 'An error occurred while updating the company.']);

        $this->assertDatabaseHas('companies', $validUpdateData);
    }

    public function testDestroy(): void
    {
        $response = $this->delete("/api/companies/999");

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Company not found']);

        $company = Company::factory()->create();

        $response = $this->delete("/api/companies/{$company->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Company deleted successfully']);

        $response = $this->delete("/api/companies/{$company->id}");
    }
}
