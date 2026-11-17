<?php

use App\Models\Block;
use App\Models\Topic;
use App\Models\User;

it('rejects a new block without title, content or a real topic', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('blocks.store'), ['topic_id' => 9999])
        ->assertSessionHasErrors(['topic_id', 'title', 'content']);
});

it('creates a block owned by the logged-in user, ignoring a fake user_id', function () {
    $user = User::factory()->create();
    $victim = User::factory()->create();
    $topic = Topic::factory()->create();

    $this->actingAs($user)->post(route('blocks.store'), [
        'topic_id' => $topic->id, 'title' => 'Hello', 'content' => 'World', 'user_id' => $victim->id,
    ]);

    expect(Block::first()->user_id)->toBe($user->id);
});

it('refuses a duplicate topic name', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('topics.store'), ['topicname' => 'PHP']);
    $this->actingAs($user)->post(route('topics.store'), ['topicname' => 'PHP'])
        ->assertSessionHasErrors('topicname');
});