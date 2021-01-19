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
        Person::factory(60)->create([
            'group_id' => $group->id
        ]);
        $response = $this->get('/api/persons');
        $response->assertStatus(200);
        $responseArray = json_decode($response->getContent());
        $this->assertCount(15, $responseArray->data);
        $this->assertEquals(60, $responseArray->total);
        $this->assertEquals(4, $responseArray->last_page);
        $this->assertEquals(15, $responseArray->per_page);
        $this->assertEquals(1, $responseArray->current_page);
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

    public function testIndexMethodWithChangeCurrentPage()
    {
        $group = Group::factory()->create();
        Person::factory(60)->create([
            'group_id' => $group->id
        ]);
        $response = $this->get('/api/persons/?page=2');
        $response->assertStatus(200);
        $responseArray = json_decode($response->getContent());
        $this->assertEquals(2, $responseArray->current_page);
    }

    public function testStoreMethodWithSuccessResult()
    {
        $group = Group::factory()->create();
        $data = [
            'group_id' => $group->id,
            'code' => 145,
            'company' => 'Empresa de Teste',
            'name' => 'Nome de Teste',
            'cpf_cnpj' => '078.825.800-15',
            'rg_ie' => '35.420.956-5'
        ];
        $response = $this->post('/api/persons', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'group_id',
            'company',
            'name',
            'cpf_cnpj',
            'rg_ie',
            'created_at',
            'updated_at'
        ]);
    }

    public function testStoreMethodWithValidationFails()
    {
        $data = [];
        $response = $this->post('/api/persons', $data);
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'group_id',
            'name',
            'cpf_cnpj'
        ]);
        $response->assertExactJson([
            'group_id' => ['O campo é obrigatório!'],
            'name' => ['O campo é obrigatório!'],
            'cpf_cnpj' => ['O campo é obrigatório!'],
        ]);
    }
}
