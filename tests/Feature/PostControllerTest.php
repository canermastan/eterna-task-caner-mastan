<?php

use App\Core\Enums\PostStatus;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->admin = User::factory()->create(['role_id' => 1]);
    $this->writer = User::factory()->create(['role_id' => 2]);
    $this->user = User::factory()->create(['role_id' => 3]);

    $this->category = Category::factory()->create();

    Config::set('broadcasting.default', 'dummy');
});

it('can get posts list', function () {
    Post::factory()
        ->count(3)
        ->published()
        ->create([
            'user_id' => $this->admin->id,
        ]);

    $this->get('/api/posts')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});
    
it('returns a single post by slug', function () {
    $post = Post::factory()->published()->create([
        'user_id' => $this->admin->id,
    ]);

    $post->categories()->attach($this->category->id);
    
    $response = $this->get('/api/posts/slug/' . $post->slug)
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'content',
                'slug',
                'coverImage',
                'categories' => [
                    '*' => ['id', 'name']
                ],
                'user' => [
                    'id',
                    'first_name', 
                    'last_name'
                ],
                'status',
                'publishedAt',
                'createdAt'
            ]
        ])
        ->assertJson([
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'slug' => $post->slug,
                'status' => $post->status->value,
                'user' => [
                    'id' => $this->admin->id,
                    'first_name' => $this->admin->first_name,
                    'last_name' => $this->admin->last_name,
                ],
                'categories' => [
                    [
                        'id' => $this->category->id,
                        'name' => $this->category->name,
                    ]
                ]
            ]
        ]);
        
    expect($response->json('data.status'))->toBe('published');
    expect($response->json('data.publishedAt'))->not->toBeNull();
});


it('returns a single post by id', function () {
    $post = Post::factory()->published()->create([
        'user_id' => $this->admin->id,
    ]);

    $post->categories()->attach($this->category->id);
    
    $response = $this->get('/api/posts/' . $post->id)
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'content',
                'slug',
                'coverImage',
                'categories' => [
                    '*' => ['id', 'name']
                ],
                'user' => [
                    'id',
                    'first_name', 
                    'last_name'
                ],
                'status',
                'publishedAt',
                'createdAt'
            ]
        ])
        ->assertJson([
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'slug' => $post->slug,
                'status' => $post->status->value,
                'user' => [
                    'id' => $this->admin->id,
                    'first_name' => $this->admin->first_name,
                    'last_name' => $this->admin->last_name,
                ],
                'categories' => [
                    [
                        'id' => $this->category->id,
                        'name' => $this->category->name,
                    ]
                ]
            ]
        ]);
        
    expect($response->json('data.status'))->toBe('published');
    expect($response->json('data.publishedAt'))->not->toBeNull();
});


it('can create a post', function () {
    $this->actingAs($this->admin);
    $response = $this->post('/api/posts', [
        'title' => 'Test Post',
        'content' => 'This is a test post',
        'slug' => 'test-post',
        'categoryIds' => [$this->category->id],
    ]);
    $response->assertCreated();
});

it('can update a post', function () {
    $this->actingAs($this->admin);
    $post = Post::factory()->published()->create([
        'user_id' => $this->admin->id,
    ]);

    $response = $this->post('/api/posts/' . $post->id, [
        'title' => 'Updated Post',
        'content' => 'This is an updated post',
        'slug' => 'updated-post',
        'categoryIds' => [$this->category->id],
    ]);
    $response->assertOk();
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Updated Post',
        'content' => 'This is an updated post',
    ]);
});

it('can delete a post', function () {
    $this->actingAs($this->admin);
    $post = Post::factory()->published()->create([
        'user_id' => $this->admin->id,
    ]);

    $response = $this->delete('/api/posts/' . $post->id);
    $response->assertNoContent();
    $this->assertSoftDeleted('posts', ['id' => $post->id]);
});

it('can toggle draft/published status of a post', function () {
    $this->actingAs($this->admin);

    $post = Post::factory()->published()->create([
        'user_id' => $this->writer->id,
    ]);

    $response = $this->patch('/api/posts/' . $post->id . '/toggle-draft-published');
    $response->assertOk();
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'status' => PostStatus::DRAFT->value,
    ]);
});

it('can get all posts for admin', function () {
    $this->actingAs($this->admin);

    Post::factory()->count(5)->create([
        'user_id' => $this->writer->id,
    ]);

    Post::factory()->count(5)->create([
        'user_id' => $this->admin->id,
    ]);

    $response = $this->get('/api/posts/all');
    $response->assertOk();
    $response->assertJsonCount(10, 'data');
});

it('can get my posts', function () {
    $this->actingAs($this->writer);

    Post::factory()->count(5)->create([
        'user_id' => $this->writer->id,
    ]);

    $response = $this->get('/api/posts/my-posts');
    $response->assertOk();
    $response->assertJsonCount(5, 'data');
});

it ('cannot create a post', function () {
    $this->actingAs($this->user);
    $response = $this->post('/api/posts', [
        'title' => 'Test Post',
        'content' => 'This is a test post',
        'slug' => 'test-post',
        'categoryIds' => [$this->category->id],
    ]);

    $response->assertForbidden();
});

it ('cannot update a post', function () {
    $this->actingAs($this->user);
    $post = Post::factory()->published()->create([
        'user_id' => $this->writer->id,
    ]);

    $response = $this->post('/api/posts/' . $post->id, [
        'title' => 'Updated Post',
        'content' => 'This is an updated post',
        'slug' => 'updated-post',
        'categoryIds' => [$this->category->id],
    ]);

    $response->assertForbidden();
});

it ('cannot toggle draft/published status of a post', function () {
    $this->actingAs($this->user);
    $post = Post::factory()->published()->create([
        'user_id' => $this->writer->id,
    ]);

    $response = $this->patch('/api/posts/' . $post->id . '/toggle-draft-published');
    $response->assertForbidden();
});

it ('cannot get all posts for admin', function () {
    $this->actingAs($this->user);

    $response = $this->get('/api/posts/all');
    $response->assertForbidden();
});

it ('cannot get my posts', function () {
    $this->actingAs($this->user);

    $response = $this->get('/api/posts/my-posts');
    $response->assertForbidden();
});