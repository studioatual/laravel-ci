<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Group;
use App\Models\Person;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PersonControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndexMethodWithExpectedPaginateResults()
    {
        $group = Group::factory()->create();
        Person::factory(50)->create([
            'group_id' => $group->id
        ]);
        $response = $this->get('/api/persons/');
        $response->assertStatus(200);
        $responseArray = json_decode($response->getContent());
        $this->assertCount(15, $responseArray->data);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'group_id',
                    'company',
                    'name',
                    'cpf_cnpj',
                    'rg_ie',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
    }
}
