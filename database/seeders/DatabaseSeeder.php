<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskStatus;
use Database\Factories\TaskFactory;
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
        \App\Models\Role::factory(1)->create(['value' => 'manager', 'title' => 'manager']);
        \App\Models\Role::factory(1)->create(['value' => 'user', 'title' => 'user']);
        \App\Models\User::factory(1)->create([
            'role_id' => 1,
            'email' => 'manager@manager.com'
        ]);
        \App\Models\User::factory(10)->create();
        TaskStatus::create(
            [
                'title' => 'created',
                'value' => 'created'
            ]);
        TaskStatus::create([
            'title' => 'assigned',
            'value' => 'assigned'
        ]);
        TaskStatus::create([
            'title' => 'in progress',
            'value' => 'in-progress'
        ]);
        TaskStatus::create([
            'title' => 'done',
            'value' => 'done'
        ]);
        Task::factory(25)->create();
    }
}
