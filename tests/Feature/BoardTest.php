<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Board;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected Space $space;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        
        // Создаем пространство для основного пользователя
        $this->space = Space::factory()->create(['user_id' => $this->user->id]);
    }

    /**
     * Тест: Гость не может видеть форму создания доски
     */
    public function test_guest_cannot_access_create(): void
    {
        $response = $this->get(route('boards.create', $this->space));
        $response->assertRedirect(route('login'));
    }

    /**
     * Тест: Чужой пользователь не может видеть форму создания доски в чужом пространстве
     */
    public function test_other_user_cannot_access_create_in_other_space(): void
    {
        $response = $this->actingAs($this->otherUser)->get(route('boards.create', $this->space));
        $response->assertForbidden();
    }

    /**
     * Тест: Владелец пространства может видеть форму создания доски
     */
    public function test_owner_can_access_create(): void
    {
        $response = $this->actingAs($this->user)->get(route('boards.create', $this->space));
        $response->assertOk();
        $response->assertViewIs('boards.create');
        $response->assertViewHas('space');
    }

    /**
     * Тест: Успешное создание доски
     */
    public function test_owner_can_store_board(): void
    {
        $data = [
            'name' => 'Новая доска',
        ];

        $response = $this->actingAs($this->user)->post(route('boards.store', $this->space), $data);

        $response->assertRedirect(route('spaces.show', $this->space));
        
        $this->assertDatabaseHas('boards', [
            'name' => 'Новая доска',
            'space_id' => $this->space->id,
        ]);
    }

    /**
     * Тест: Валидация имени доски при создании
     */
    public function test_board_requires_name_on_store(): void
    {
        $data = [
            'name' => '',
        ];

        $response = $this->actingAs($this->user)->post(route('boards.store', $this->space), $data);
        $response->assertSessionHasErrors('name');
    }

    /**
     * Тест: Чужой пользователь не может создать доску в чужом пространстве
     */
    public function test_other_user_cannot_store_board_in_other_space(): void
    {
        $data = [
            'name' => 'Взломанная доска',
        ];

        $response = $this->actingAs($this->otherUser)->post(route('boards.store', $this->space), $data);
        $response->assertForbidden();
        
        $this->assertDatabaseMissing('boards', [
            'name' => 'Взломанная доска',
            'space_id' => $this->space->id,
        ]);
    }

    /**
     * Тест: Просмотр доски (Show)
     */
    public function test_owner_can_view_board(): void
    {
        $board = Board::factory()->forSpace($this->space)->create();
        
        $response = $this->actingAs($this->user)->get(route('boards.show', [$this->space, $board]));
        
        $response->assertOk();
        $response->assertViewHas('board');
        $response->assertViewHas('tasks');
    }

    /**
     * Тест: Чужой пользователь не может просмотреть доску в чужом пространстве
     */
    public function test_other_user_cannot_view_board_in_other_space(): void
    {
        $board = Board::factory()->forSpace($this->space)->create();
        
        $response = $this->actingAs($this->otherUser)->get(route('boards.show', [$this->space, $board]));
        
        $response->assertForbidden();
    }

    /**
     * Тест: Обновление имени доски
     */
    public function test_owner_can_update_board(): void
    {
        $board = Board::factory()->forSpace($this->space)->create(['name' => 'Старое имя']);
        
        $data = [
            'name' => 'Новое имя доски',
        ];

        $response = $this->actingAs($this->user)->put(route('boards.update', [$this->space, $board]), $data);
        
        $response->assertRedirect(); // redirect()->back()
        
        $this->assertDatabaseHas('boards', [
            'id' => $board->id,
            'name' => 'Новое имя доски',
        ]);
    }

    /**
     * Тест: Чужой пользователь не может обновить доску
     */
    public function test_other_user_cannot_update_board(): void
    {
        $board = Board::factory()->forSpace($this->space)->create(['name' => 'Оригинал']);
        
        $data = [
            'name' => 'Взломано',
        ];

        $response = $this->actingAs($this->otherUser)->put(route('boards.update', [$this->space, $board]), $data);
        
        $response->assertForbidden();
        
        $this->assertDatabaseHas('boards', [
            'id' => $board->id,
            'name' => 'Оригинал',
        ]);
    }

    /**
     * Тест: Удаление доски
     */
    public function test_owner_can_delete_board(): void
    {
        $board = Board::factory()->forSpace($this->space)->create();
        
        $response = $this->actingAs($this->user)->delete(route('boards.destroy', [$this->space, $board]));
        
        $response->assertRedirect(route('spaces.show', $this->space));
        $this->assertDatabaseMissing('boards', ['id' => $board->id]);
    }

    /**
     * Тест: Чужой пользователь не может удалить доску
     */
    public function test_other_user_cannot_delete_board(): void
    {
        $board = Board::factory()->forSpace($this->space)->create();
        
        $response = $this->actingAs($this->otherUser)->delete(route('boards.destroy', [$this->space, $board]));
        
        $response->assertForbidden();
        $this->assertDatabaseHas('boards', ['id' => $board->id]);
    }
}