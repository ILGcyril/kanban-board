<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SpaceTest extends TestCase
{
    use RefreshDatabase; // Очищает БД после каждого теста

    protected User $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Создаем двух пользователей: владельца и чужака
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    /**
     * Тест: Гость не может видеть список пространств
     */
    public function test_guest_cannot_access_index(): void
    {
        $response = $this->get(route('spaces.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Тест: Авторизованный пользователь видит свой список пространств
     */
    public function test_user_can_see_own_spaces_index(): void
    {
        $space = Space::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('spaces.index'));

        $response->assertOk();
        $response->assertViewHas('spaces');
        $response->assertSee($space->name);
    }

    /**
     * Тест: Пользователь не видит пространства других людей в списке
     */
    public function test_user_cannot_see_other_users_spaces_in_index(): void
    {
        $otherSpace = Space::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)->get(route('spaces.index'));

        $response->assertOk();
        $response->assertDontSee($otherSpace->name);
    }

    /**
     * Тест: Гость не может видеть форму создания
     */
    public function test_guest_cannot_access_create(): void
    {
        $response = $this->get(route('spaces.create'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Тест: Авторизованный пользователь видит форму создания
     */
    public function test_user_can_see_create_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('spaces.create'));
        $response->assertOk();
        $response->assertViewIs('spaces.create');
    }

    /**
     * Тест: Успешное создание пространства
     */
    public function test_user_can_store_space(): void
    {
        $data = [
            'name' => 'Новое пространство',
            'description' => 'Описание для теста',
        ];

        $response = $this->actingAs($this->user)->post(route('spaces.store'), $data);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('spaces', [
            'name' => 'Новое пространство',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Тест: Валидация при создании (пустое имя)
     */
    public function test_space_requires_name_on_store(): void
    {
        $data = [
            'name' => '',
            'description' => 'Тест',
        ];

        $response = $this->actingAs($this->user)->post(route('spaces.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Тест: Гость не может просматривать пространство
     */
    public function test_guest_cannot_view_space(): void
    {
        $space = Space::factory()->create(['user_id' => $this->user->id]);
        $response = $this->get(route('spaces.show', $space));
        $response->assertRedirect(route('login'));
    }

    /**
     * Тест: Владелец может просматривать свое пространство
     */
    public function test_owner_can_view_space(): void
    {
        $space = Space::factory()->create(['user_id' => $this->user->id]);
        $response = $this->actingAs($this->user)->get(route('spaces.show', $space));
        $response->assertOk();
        $response->assertViewHas('space');
    }

    /**
     * Тест: Чужой пользователь НЕ может просматривать пространство (Policy check)
     */
    public function test_other_user_cannot_view_space(): void
    {
        $space = Space::factory()->create(['user_id' => $this->user->id]);
        
        // Пытаемся зайти под другим юзером
        $response = $this->actingAs($this->otherUser)->get(route('spaces.show', $space));
        
        // Должен получить 403 Forbidden из-за политики
        $response->assertForbidden(); 
    }

    /**
     * Тест: Владелец может редактировать пространство
     */
    public function test_owner_can_edit_space(): void
    {
        $space = Space::factory()->create(['user_id' => $this->user->id]);
        $response = $this->actingAs($this->user)->get(route('spaces.edit', $space));
        $response->assertOk();
        $response->assertViewHas('space');
    }

    /**
     * Тест: Чужой пользователь НЕ может редактировать пространство
     */
    public function test_other_user_cannot_edit_space(): void
    {
        $space = Space::factory()->create(['user_id' => $this->user->id]);
        $response = $this->actingAs($this->otherUser)->get(route('spaces.edit', $space));
        $response->assertForbidden();
    }

    /**
     * Тест: Успешное обновление пространства
     */
    public function test_owner_can_update_space(): void
    {
        $space = Space::factory()->create(['user_id' => $this->user->id, 'name' => 'Старое имя']);
        
        $data = [
            'name' => 'Новое имя',
            'description' => 'Новое описание',
        ];

        $response = $this->actingAs($this->user)->put(route('spaces.update', $space), $data);

        $response->assertRedirect(route('spaces.show', $space));
        
        $this->assertDatabaseHas('spaces', [
            'id' => $space->id,
            'name' => 'Новое имя',
        ]);
    }

    /**
     * Тест: Чужой пользователь НЕ может обновить пространство
     */
    public function test_other_user_cannot_update_space(): void
    {
        $space = Space::factory()->create(['user_id' => $this->user->id]);
        
        $data = [
            'name' => 'Взломанное имя',
            'description' => 'Взломанное описание',
        ];

        $response = $this->actingAs($this->otherUser)->put(route('spaces.update', $space), $data);
        
        $response->assertForbidden();
        
        // Проверяем, что данные не изменились
        $this->assertDatabaseMissing('spaces', [
            'id' => $space->id,
            'name' => 'Взломанное имя',
        ]);
    }

    /**
     * Тест: Владелец может удалить пространство
     */
    public function test_owner_can_delete_space(): void
    {
        $space = Space::factory()->create(['user_id' => $this->user->id]);
        
        $response = $this->actingAs($this->user)->delete(route('spaces.destroy', $space));
        
        $response->assertRedirect(route('spaces.index'));
        $this->assertDatabaseMissing('spaces', ['id' => $space->id]);
    }

    /**
     * Тест: Чужой пользователь НЕ может удалить пространство
     */
    public function test_other_user_cannot_delete_space(): void
    {
        $space = Space::factory()->create(['user_id' => $this->user->id]);
        
        $response = $this->actingAs($this->otherUser)->delete(route('spaces.destroy', $space));
        
        $response->assertForbidden();
        $this->assertDatabaseHas('spaces', ['id' => $space->id]);
    }
}