<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    
    $this->user = User::factory()->create(['role_id' => 1]);
    $this->category = Category::factory()->create();

    Config::set('broadcasting.default', 'dummy');
});

it('can get posts list', function () {
    $posts = Post::factory()
        ->count(3)
        ->published()
        ->create([
            'user_id' => $this->user->id,
        ]);

    $this->get('/api/posts')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});
    
it('returns a single post', function () {
    $post = Post::factory()->published()->create([
        'user_id' => $this->user->id,
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
                    'id' => $this->user->id,
                    'first_name' => $this->user->first_name,
                    'last_name' => $this->user->last_name,
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
    $this->actingAs($this->user);
    $response = $this->post('/api/posts', [
        'title' => 'Test Post',
        'content' => 'This is a test post',
        'slug' => 'test-post',
        'categoryIds' => [$this->category->id],
    ]);
    $response->assertCreated();
});