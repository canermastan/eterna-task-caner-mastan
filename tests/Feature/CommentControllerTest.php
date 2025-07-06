<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->admin = User::factory()->create(['role_id' => 1]);
    $this->writer = User::factory()->create(['role_id' => 2]);
    $this->user = User::factory()->create(['role_id' => 3]);

    $this->post = Post::factory()->create([
        'user_id' => $this->admin->id,
    ]);

    Config::set('broadcasting.default', 'dummy');
});

it('can create a comment', function () {
    $this->actingAs($this->writer);

    $response = $this->post('/api/comments', [
        'post_id' => $this->post->id,
        'content' => 'Test Comment',
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('comments', [
        'post_id' => $this->post->id,
        'content' => 'Test Comment',
    ]);
});

it('can get comments list for admins', function () {
    $this->actingAs($this->admin);
    Comment::factory()
        ->count(3)
        ->create([
            'post_id' => $this->post->id,
        ]);
    $this->get('/api/comments')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('can get comments for a post', function () {
    $this->actingAs($this->user);
    Comment::factory()
        ->count(3)
        ->approved()
        ->create([
            'post_id' => $this->post->id,
        ]);
    $this->get('/api/comments/post/' . $this->post->id)
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('can update a comment', function () {
    $this->actingAs($this->admin);

    $comment = Comment::factory()->create([
        'post_id' => $this->post->id,
    ]);

    $response = $this->put('/api/comments/' . $comment->id, [
        'content' => 'Updated Comment',
    ]);
    
    $response->assertOk();
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'content' => 'Updated Comment',
    ]);
});

it('can delete a comment', function () {
    $this->actingAs($this->admin);

    $comment = Comment::factory()->create([
        'post_id' => $this->post->id,
    ]);

    $response = $this->delete('/api/comments/'. $comment->id);
    $response->assertNoContent();
    $this->assertSoftDeleted('comments', [
        'id' => $comment->id,
    ]);
});


it('can reply to a comment', function () {
    $this->actingAs($this->admin);

    $comment = Comment::factory()->create([
        'post_id' => $this->post->id,
    ]);

    $response = $this->post('/api/comments', [
        'post_id' => $this->post->id,
        'content' => 'Reply Comment',
        'parent_id' => $comment->id,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('comments', [
        'post_id' => $this->post->id,
        'content' => 'Reply Comment',
        'parent_id' => $comment->id,
    ]);
});

it ('cannot create a comment', function () {
    $this->actingAs($this->user);
    
    $response = $this->post('/api/comments', [
        'post_id' => $this->post->id,
        'content' => 'Reply Comment',
    ]);

    $response->assertForbidden();
});