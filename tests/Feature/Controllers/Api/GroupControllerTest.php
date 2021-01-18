<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Group;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndexMethodAndExpectTenResults()
    {
        Group::factory(10)->create();
        $response = $this->get('/api/groups');
        $response->assertStatus(200);
        $response->assertJsonCount(10);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'cnpj',
                'type',
                'active',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function testStoreMethodAndExpectSuccessResults()
    {
        $data = [
            'code' => rand(100, 999),
            'name' => 'FBS Sistemas',
            'cnpj' => '91.462.611/0001-07',
            'type' => 0,
            'active' => 1,
        ];

        $response = $this->post('/api/groups', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'cnpj',
            'type',
            'active',
            'created_at',
            'updated_at'
        ]);

        $responseArray = json_decode($response->content(), true);

        $this->assertArrayHasKey('id', $responseArray);
        $this->assertEquals(1, $responseArray['id']);

        foreach ($data as $key => $value) {
            $this->assertArrayHasKey($key, $responseArray);
            if ($key == 'cnpj') {
                $cnpj = preg_replace('/\D/', '', $data['cnpj']);
                $this->assertEquals($cnpj, $responseArray[$key]);
            } else {
                $this->assertEquals($value, $responseArray[$key]);
            }
        }
    }

    public function testRequiredValidationOfStoreMethod()
    {
        $data = [];
        $response = $this->post('/api/groups', $data);
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'name',
            'cnpj'
        ]);

        $response->assertExactJson([
            'name' => ['O campo é obrigatório!'],
            'cnpj' => ['O campo é obrigatório!'],
        ]);
    }

    public function testFieldNameMaxValidationOfStoreMethod()
    {
        $data = [
            'code' => rand(100, 999),
            'name' => 'asdgfsdagsadg asdgasgsd asdgsdgasdg asdgsadgsdag asdgasdgas asdgasdga',
            'cnpj' => '91.462.611/0001-07',
            'type' => 0,
            'active' => 1,
        ];
        $response = $this->post('/api/groups', $data);
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'name'
        ]);

        $response->assertExactJson([
            'name' => ['Máximo de 50 caracteres!']
        ]);
    }

    public function testFieldCnpjInvalidValidationOfStoreMethod()
    {
        $data = [
            'code' => rand(100, 999),
            'name' => 'FBS Sistemas',
            'cnpj' => '91.462.611/0001-08',
            'type' => 0,
            'active' => 1,
        ];
        $response = $this->post('/api/groups', $data);
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'cnpj'
        ]);

        $response->assertExactJson([
            'cnpj' => ['CNPJ é inválido!']
        ]);
    }

    public function testUniqueValidationOfMethodStore()
    {
        $data = [
            'code' => rand(100, 999),
            'name' => 'FBS Sistemas',
            'cnpj' => '91462611000107',
            'type' => 0,
            'active' => 1,
        ];
        Group::create($data);
        $response = $this->post('/api/groups', $data);
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'cnpj'
        ]);

        $response->assertExactJson([
            'cnpj' => [$data['cnpj'] . ' já em uso!']
        ]);
    }

    public function testShowMethodAndExpectSuccessResult()
    {
        $data = [
            'code' => rand(100, 999),
            'name' => 'FBS Sistemas',
            'cnpj' => '91462611000107',
            'type' => 0,
            'active' => 1,
        ];
        $group = Group::create($data);
        $response = $this->get('/api/groups/' . $group->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'cnpj',
            'type',
            'active',
            'created_at',
            'updated_at'
        ]);
    }

    public function testShowMethodAndExpectNotFound()
    {
        $response = $this->get('/api/groups/1');
        $response->assertStatus(404);
        $response->assertNotFound();
    }

    public function testUpdateMethodAndExpectSuccessResult()
    {
        $data = [
            'code' => rand(100, 999),
            'name' => 'FBS',
            'cnpj' => '91462611000107',
            'type' => 0,
            'active' => 0,
        ];
        $group = Group::create($data);
        $dataUpdate = [
            'code' => 145,
            'name' => 'FBS Sistemas',
            'cnpj' => '35.537.792/0001-12',
            'type' => 1,
            'active' => 1,
        ];
        $response = $this->put('/api/groups/' . $group->id, $dataUpdate);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'cnpj',
            'type',
            'active',
            'created_at',
            'updated_at'
        ]);
        $response->assertJson([
            'code' => 145,
            'name' => 'FBS Sistemas',
            'cnpj' => '35537792000112',
            'type' => 1,
            'active' => 1,
        ]);
    }
}
