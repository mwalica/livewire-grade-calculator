<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

        //$levels = [1, 0.91, 0.71, 0.51, 0.41];

        Grade::create([
            'name' => 'celujący',
            'percentage' => 100,
        ]);
        Grade::create([
            'name' => 'bardzo dobry',
            'percentage' => 91,
        ]);
        Grade::create([
            'name' => 'dobry',
            'percentage' => 71,
        ]);
        Grade::create([
            'name' => 'dostateczny',
            'percentage' => 51,
        ]);
        Grade::create([
            'name' => 'dopuszczający',
            'percentage' => 41,
        ]);

    }
}
