<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create(['username' => 'admin', 'email' => 'admin@example.com']);
        User::factory(20)->create();
        Machine::factory(300)->create();
        collect(['engineering', 'compsci', 'maths', 'laptop', 'labmachine', 'macos', 'windows'])->each(fn ($tagName) => Tag::create(['name' => $tagName]));
        $tags = Tag::all();
        Machine::all()->each(function ($machine) use ($tags) {
            $machine->tags()->attach($tags->random(3));
        });
    }
}
