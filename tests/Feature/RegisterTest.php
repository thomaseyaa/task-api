<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use WithFaker;

    public function test_no_input() {
        $response = $this->postJson('/api/auth/register');
        $response
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_invalid_input() {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->name,
            'password' => $this->faker->password(8),
            'device_name' => "mac"
        ];

        $response = $this->postJson('/api/auth/register', $data);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_user_already_registrered() {
        $password = $this->faker->password(8);

        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make($password),
        ];

        $user = User::create($userData);

        $formData = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'device_name' => "mac"
        ];

        $response = $this->postJson('/api/auth/register', $formData);
        $response
            ->assertStatus(409)
            ->assertJsonStructure(['error']);
    }

    public function test_register_with_success() {
        $formData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make($this->faker->password(8)),
            'device_name' => "mac"
        ];

        $response = $this->postJson('/api/auth/register', $formData);
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['token', 'email', 'created_at', 'name'])
            ->assertJson(['email' => $formData['email'], 'name' => $formData['name']]);
    }
}
