<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUserWithToken(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [$user, $token];
    }

    public function test_user_must_be_authenticated_to_access_tasks(): void
    {
        $this->getJson('/api/tasks')->assertStatus(401);
        $this->postJson('/api/tasks', [])->assertStatus(401);
    }

    public function test_authenticated_user_can_list_own_tasks(): void
    {
        [$user, $token] = $this->actingAsUserWithToken();

        Task::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        // чужие задачи
        Task::factory()->count(3)->create();

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/tasks');

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_authenticated_user_can_create_task(): void
    {
        [$user, $token] = $this->actingAsUserWithToken();

        $payload = [
            'title' => 'Новая задача',
            'text' => 'Текст новой задачи',
        ];

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tasks', $payload);

        $response
            ->assertCreated()
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Новая задача',
                    'text' => 'Текст новой задачи',
                    'completed' => false,
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Новая задача',
            'text' => 'Текст новой задачи',
            'completed' => false,
        ]);
    }

    public function test_create_task_validation_error(): void
    {
        [$user, $token] = $this->actingAsUserWithToken();

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tasks', []);

        $response->assertStatus(422);
    }

    public function test_user_can_update_own_task(): void
    {
        [$user, $token] = $this->actingAsUserWithToken();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Старая задача',
            'text' => 'Текст старой задачи',
            'completed' => false,
        ]);

        $payload = [
            'completed' => true,
        ];

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/tasks/{$task->id}", $payload);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'completed' => true,
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $user->id,
            'completed' => true,
        ]);
    }

    public function test_user_cannot_update_foreign_task(): void
    {
        [$user, $token] = $this->actingAsUserWithToken();

        $foreignTask = Task::factory()->create(); // без user_id текущего пользователя

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/tasks/{$foreignTask->id}", [
                'text' => 'Попытка изменить',
            ]);

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false
            ]);
    }

    public function test_user_can_delete_own_task(): void
    {
        [$user, $token] = $this->actingAsUserWithToken();

        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/tasks/{$task->id}");

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Task deleted successfully',
            ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_cannot_delete_foreign_task(): void
    {
        [$user, $token] = $this->actingAsUserWithToken();

        $foreignTask = Task::factory()->create();

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/tasks/{$foreignTask->id}");

        $response->assertStatus(401);
    }

    public function test_user_can_clear_completed_tasks(): void
    {
        [$user, $token] = $this->actingAsUserWithToken();

        // выполненные задачи текущего пользователя
        Task::factory()->count(2)->create([
            'user_id' => $user->id,
            'completed' => true,
        ]);

        // невыполненная задача (должна остаться)
        $activeTask = Task::factory()->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);

        // выполненные задачи другого пользователя (не должны затронуться)
        Task::factory()->count(2)->create([
            'completed' => true,
        ]);

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/tasks/completed/clear');

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);

        // выполненные задачи текущего пользователя должны быть удалены
        $this->assertDatabaseMissing('tasks', [
            'user_id' => $user->id,
            'completed' => true,
        ]);

        // невыполненная задача должна остаться
        $this->assertDatabaseHas('tasks', [
            'id' => $activeTask->id,
            'completed' => false,
        ]);
    }
}


