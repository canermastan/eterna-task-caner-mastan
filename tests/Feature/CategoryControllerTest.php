<?php

use App\Models\Category;
use App\Models\User;
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

it('can get all categories', function () {
    $this->actingAs($this->user);

    Category::factory()->count(5)->create();

    $response = $this->get('/api/categories');
    $response->assertStatus(200);
    $response->assertJsonCount(6, 'data'); // this is 6 because we create 1 more category in beforeEach
});

it('can create a category', function () {
    $this->actingAs($this->admin);

    $response = $this->post('/api/categories', [
        'name' => 'Test Category',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
    ]);
});

it('can update a category', function () {
    $this->actingAs($this->admin);
    $response = $this->put('/api/categories/' . $this->category->id, [
        'name' => 'Updated Category',
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('categories', [
        'name' => 'Updated Category',
    ]);
});

it('can delete a category', function () {
    $this->actingAs($this->admin);
    $response = $this->delete('/api/categories/'. $this->category->id);
    $response->assertStatus(204);
    $this->assertDatabaseMissing('categories', [
        'id' => $this->category->id,
    ]);
});

it ('cannot create a category', function () {
    $this->actingAs($this->user);
    $response = $this->post('/api/categories', [
        'name' => 'Test Category',
    ]);
    $response->assertForbidden();
});

it ('cannot update a category', function () {
    $this->actingAs($this->user);
    $response = $this->put('/api/categories/'. $this->category->id, [
        'name' => 'Updated Category',
    ]);
    $response->assertForbidden();
});

it ('cannot delete a category', function () {
    $this->actingAs($this->user);
    $response = $this->delete('/api/categories/'. $this->category->id);
    $response->assertForbidden();
});