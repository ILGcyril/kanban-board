<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Board;
use App\Models\Space;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected Space $space;
    protected Board $board;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        
        // Создаем иерархию: Юзер -> Пространство -> Доска
        $this->space = Space::factory()->create(['user_id' => $this->user->id]);
        $this->board = Board::factory()->create(['space_id' => $this->space->id]);
    }

    /**
     * Тест: Гость не может создать задачу
     */
    public function test_guest_cannot_store_task(): void
    {
        $data = [
            'title' => 'Задача',
            'status' => 'todo',
        ];
        
        $response = $this->post(route('tasks.store', [$this->space, $this->board]), $data);
        $response->assertRedirect(route('login'));
    }

    /**
     * Тест: Чужой пользователь не может создать задачу в чужой доске
     */
    public function test_other_user_cannot_store_task_in_other_board(): void
    {
        $data = [
            'title' => 'Чужая задача',
            'status' => 'todo',
        ];
        
        $response = $this->actingAs($this->otherUser)->post(route('tasks.store', [$this->space, $this->board]), $data);
        $response->assertForbidden();
        
        $this->assertDatabaseMissing('tasks', ['title' => 'Чужая задача']);
    }

    /**
     * Тест: Владелец может создать задачу
     */
    public function test_owner_can_store_task(): void
    {
        $data = [
            'title' => 'Новая задача',
            'description' => 'Описание задачи',
            'status' => 'todo',
        ];
        
        $response = $this->actingAs($this->user)->post(route('tasks.store', [$this->space, $this->board]), $data);
        
        $response->assertRedirect(); // redirect()->back()
        
        $this->assertDatabaseHas('tasks', [
            'title' => 'Новая задача',
            'board_id' => $this->board->id,
            'status' => 'todo',
        ]);
    }

    /**
     * Тест: Валидация - задача требует название
     */
    public function test_task_requires_title_on_store(): void
    {
        $data = [
            'title' => '',
            'status' => 'todo',
        ];
        
        $response = $this->actingAs($this->user)->post(route('tasks.store', [$this->space, $this->board]), $data);
        $response->assertSessionHasErrors('title');
    }

    /**
     * Тест: Валидация - статус должен быть корректным
     */
    public function test_task_status_must_be_valid_enum(): void
    {
        $data = [
            'title' => 'Задача',
            'status' => 'invalid_status',
        ];
        
        $response = $this->actingAs($this->user)->post(route('tasks.store', [$this->space, $this->board]), $data);
        $response->assertSessionHasErrors('status');
    }

    /**
     * Тест: Обновление задачи (смена статуса и названия)
     */
    public function test_owner_can_update_task(): void
    {
        $task = Task::factory()->forBoard($this->board)->create([
            'title' => 'Старое название',
            'status' => 'todo',
        ]);
        
        $data = [
            'title' => 'Новое название',
            'status' => 'in_progress',
            'description' => 'Новое описание',
        ];
        
        $response = $this->actingAs($this->user)->put(route('tasks.update', [$this->space, $this->board, $task]), $data);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Новое название',
            'status' => 'in_progress',
            'description' => 'Новое описание',
        ]);
    }

    /**
     * Тест: Чужой пользователь не может обновить задачу
     */
    public function test_other_user_cannot_update_task(): void
    {
        $task = Task::factory()->forBoard($this->board)->create(['title' => 'Оригинал']);
        
        $data = [
            'title' => 'Взломано',
            'status' => 'done',
        ];
        
        $response = $this->actingAs($this->otherUser)->put(route('tasks.update', [$this->space, $this->board, $task]), $data);
        
        $response->assertForbidden();
        
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Оригинал',
        ]);
    }

    /**
     * Тест: Удаление задачи
     */
    public function test_owner_can_delete_task(): void
    {
        $task = Task::factory()->forBoard($this->board)->create();
        
        $response = $this->actingAs($this->user)->delete(route('tasks.destroy', [$this->space, $this->board, $task]));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * Тест: Чужой пользователь не может удалить задачу
     */
    public function test_other_user_cannot_delete_task(): void
    {
        $task = Task::factory()->forBoard($this->board)->create();
        
        $response = $this->actingAs($this->otherUser)->delete(route('tasks.destroy', [$this->space, $this->board, $task]));
        
        $response->assertForbidden(); 
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}