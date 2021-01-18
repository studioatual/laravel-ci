<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Group::factory(5)->create()->each(function (Group $group) {
            Person::factory(rand(5, 10))->create([
                'group_id' => $group->id
            ])->each(function (Person $person) {
                User::factory(rand(0, 1))->create();
            });
        });
    }
}
