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

    public function testIndexMethodWithExpectedAllResults()
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

    public function testStoreMethodWithExpectedSuccessResult()
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

    public function testStoreMethodWithExpectedRequiredFieldsValidation()
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

    public function testStoreMethodWithExpectedMaxCharacterInvalid()
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

    public function testStoreMethodWithExpectedCnpjInvalid()
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

    public function testStoreMethodWithExpectedUniqueValidation()
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

    public function testShowMethodWithExpectedSuccessResult()
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

    public function testShowMethodWithExpectedNotFound()
    {
        $response = $this->get('/api/groups/1');
        $response->assertStatus(404);
        $response->assertNotFound();
    }

    public function testUpdateMethodWithExpectedSuccessResult()
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

    public function testUpdateMethodWithExpectedCnpjInvalid()
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
            'cnpj' => '35.537.792/0001-13',
        ];
        $response = $this->put('/api/groups/' . $group->id, $dataUpdate);
        $response->assertStatus(400);
        $response->assertExactJson([
            'cnpj' => ['CNPJ é inválido!'],
        ]);
    }

    public function testUpdateMethodWithExpectedMaxCharacterInvalid()
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
            'name' => 'asdgfsdagsadg asdgasgsd asdgsdgasdg asdgsadgsdag asdgasdgas asdgasdga',
        ];
        $response = $this->put('/api/groups/' . $group->id, $dataUpdate);
        $response->assertStatus(400);
        $response->assertExactJson([
            'name' => ['Máximo de 50 caracteres!']
        ]);
    }

    public function testUpdateMethodWithExpectedNotFound()
    {
        $response = $this->put('/api/groups/1');
        $response->assertStatus(404);
        $response->assertNotFound();
    }

    public function testUpdateMethodWithExpectedUniqueValidation()
    {
        Group::factory(1)->create([
            'cnpj' => '91462611000107'
        ]);

        $data = [
            'code' => 145,
            'name' => 'FBS Sistemas',
            'cnpj' => '35.537.792/0001-12',
            'type' => 1,
            'active' => 0,
        ];
        $group = Group::create($data);

        $updateData = [
            'code' => rand(100, 999),
            'name' => 'FBS Sistemas',
            'cnpj' => '91.462.611/0001-07',
            'type' => 0,
            'active' => 1,
        ];

        $response = $this->put('/api/groups/' . $group->id, $updateData);
        $response->assertStatus(400);
        $response->assertExactJson([
            'cnpj' => [preg_replace('/\D/', '', $updateData['cnpj']) . ' já em uso!']
        ]);
    }

    public function testDestroyMethodWithExpectedSuccessResult()
    {
        $data = [
            'code' => 145,
            'name' => 'FBS Sistemas',
            'cnpj' => '35.537.792/0001-12',
            'type' => 1,
            'active' => 0,
        ];

        $group = Group::create($data);

        $response = $this->delete('/api/groups/' . $group->id);
        $response->assertStatus(200);
        $response->assertExactJson(['result' => 'Ok']);
    }

    public function testDestroyMethodWithExpectedNotFound()
    {
        $response = $this->delete('/api/groups/1');
        $response->assertStatus(404);
        $response->assertNotFound();
    }
}
