<?php

use App\Models\Block;
use App\Models\Tag;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

it('seeds at least 5 topics and 20 blocks', function () {
    $this->seed();

    expect(Topic::count())->toBeGreaterThanOrEqual(5);
    expect(Block::count())->toBeGreaterThanOrEqual(20);
});

it('seeds tags and attaches them to every block', function () {
    $this->seed();

    expect(Block::has('tags')->count())->toBe(Block::count());
});

it('can attach several tags to one block', function () {
    $block = Block::factory()->create();
    $block->tags()->attach(Tag::factory(3)->create()->pluck('id'));

    expect($block->tags)->toHaveCount(3);
});

it('sync replaces the tags a block has', function () {
    $block = Block::factory()->create();
    [$a, $b, $c] = Tag::factory(3)->create()->all();

    $block->tags()->attach([$a->id, $b->id]);
    $block->tags()->sync([$c->id]);

    expect($block->tags()->pluck('tags.id')->all())->toBe([$c->id]);
});

it('removes the pivot rows when a block is deleted', function () {
    $block = Block::factory()->hasTags(2)->create();
    $block->delete();

    expect(DB::table('block_tag')->count())->toBe(0);
});