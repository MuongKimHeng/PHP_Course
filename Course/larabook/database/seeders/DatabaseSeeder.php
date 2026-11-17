<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Topic;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $owner = User::factory()->create([
            'name' => 'KimHeng',
            'email' => 'hengzy0209@gmail.com',
            'email_verified_at' => now(),
            'password' => '12345678'
        ]);

        $other = User::factory()->create([
            'name' => 'Demo Author',
            'email' => 'demo@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);

        $topics = Topic::factory(5)->create();

        $blocks = Block::factory(24)
            ->recycle($topics)
            ->recycle(collect([$owner, $other]))
            ->create();

        $tags = Tag::factory(8)->create();

        $blocks->each(fn (Block $b) => $b->tags()->attach(
            $tags->random(rand(1, 3))->pluck('id')
        ));

        $blocks->first()->tags()->sync($tags->take(2)->pluck('id'));

    }
}
