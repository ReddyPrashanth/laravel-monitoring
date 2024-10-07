<?php

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list todos', function () {
    $response = $this->getJson('/api/todos');

    $response->assertStatus(200);
});

it('can show a todo', function () {
    $todo = Todo::factory()->create();
    $response = $this->getJson("/api/todos/{$todo->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.attributes.title', $todo->title);
});

describe('create todo', function () {
    $title = fake()->words(4, true);
    $description = fake()->paragraph();

    it('should have a description', function () use ($title) {
        $payload = [
            'data' => [
                'attributes' => [
                    'title' => $title,
                    'description' => null
                ]
            ]
        ];

        $response = $this->postJson('/api/todos', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('message', "The data.attributes.description field is required.");
    });

    it('should have a title', function () use ($description) {
        $payload = [
            'data' => [
                'attributes' => [
                    'title' => null,
                    'description' => $description
                ]
            ]
        ];

        $response = $this->postJson('/api/todos', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('message', "The data.attributes.title field is required.");
    });

    it('can create a todo', function () use ($title, $description) {
        $payload = [
            'data' => [
                'attributes' => [
                    'title' => $title,
                    'description' => $description
                ]
            ]
        ];

        $response = $this->postJson('/api/todos', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.attributes.title', $title);
        $this->assertDatabaseHas('todos', [
            'id' => $response['data']['id']
        ]);
    });
});

it('can update a todo', function () {
    $todo = Todo::factory()->create();
    $status = 'in progress';

    $payload = [
        'data' => [
            'attributes' => [
                'title' => $todo->title,
                'description' => $todo->description,
                'status' => $status
            ]
        ]
    ];

    $response = $this->putJson("/api/todos/{$todo->id}", $payload);

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $todo->id)
        ->assertJsonPath('data.attributes.status', $status);
});

it('can delete a todo', function () {
    $todo = Todo::factory()->create();

    $response = $this->deleteJson("/api/todos/{$todo->id}");

    $response->assertStatus(200)
        ->assertJson([
            "data" => [],
            "message" => "Todo successfully deleted",
            "status" => 200
        ]);
    $this->assertDatabaseMissing('todos', $todo->toArray());
});
