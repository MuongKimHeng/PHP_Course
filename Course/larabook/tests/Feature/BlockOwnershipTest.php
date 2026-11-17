<?php

use App\Models\Block;
use App\Models\User;

function validBlockData(Block $block): array
{
    return ['topic_id' => $block->topic_id, 'title' => 'New title', 'content' => 'New content'];
}

it('sends guests to the login page', function () {
    $block = Block::factory()->create();

    $this->get(route('blocks.create'))->assertRedirect(route('login'));
    $this->get(route('blocks.edit', $block))->assertRedirect(route('login'));
    $this->delete(route('blocks.destroy', $block))->assertRedirect(route('login'));
});

it('lets the owner update and delete their block', function () {
    $owner = User::factory()->create();
    $block = Block::factory()->for($owner)->create();

    $this->actingAs($owner)->put(route('blocks.update', $block), validBlockData($block))
        ->assertRedirect(route('topics.show', $block->topic_id));
    expect($block->fresh()->title)->toBe('New title');

    $this->actingAs($owner)->delete(route('blocks.destroy', $block));
    expect(Block::find($block->id))->toBeNull();
});

it('returns 403 when someone else edits or deletes the block', function () {
    $stranger = User::factory()->create();
    $block = Block::factory()->create(['title' => 'Original']);

    $this->actingAs($stranger)->get(route('blocks.edit', $block))->assertForbidden();
    $this->actingAs($stranger)->put(route('blocks.update', $block), validBlockData($block))->assertForbidden();
    $this->actingAs($stranger)->delete(route('blocks.destroy', $block))->assertForbidden();

    expect($block->fresh()->title)->toBe('Original');
});

it('shows Edit/Delete buttons only on blocks the user owns', function () {
    $me = User::factory()->create();
    $mine = Block::factory()->for($me)->create();
    $theirs = Block::factory()->create(['topic_id' => $mine->topic_id]);

    $this->actingAs($me)->get(route('topics.show', $mine->topic_id))
        ->assertSee(route('blocks.edit', $mine), false)
        ->assertDontSee(route('blocks.edit', $theirs), false);
});