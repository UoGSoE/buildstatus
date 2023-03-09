<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Machine::factory(300)->create();
        collect(['engineering', 'compsci', 'maths', 'laptop', 'labmachine', 'macos', 'windows'])->each(fn ($tagName) => Tag::create(['name' => $tagName]));
        $tags = Tag::all();
        Machine::all()->each(function ($machine) use ($tags) {
            $machine->tags()->attach($tags->random(3));
        });
    }
}
