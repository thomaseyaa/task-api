<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use WithFaker;

    public function test_no_input() {
        $response = $this->postJson('/api/auth/login');
        $response
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_invalid_input() {
        $data = [
            'email' => $this->faker->name,
            'password' => $this->faker->password(8),
            'device_name' => "mac"
        ];

        $response = $this->postJson('/api/auth/login', $data);
        $response
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_invalid_credentials() {
        $data = [
            'email' => $this->faker->email,
            'password' => $this->faker->password(8),
            'device_name' => "mac"
        ];

        $response = $this->postJson('/api/auth/login', $data);
        $response
            ->assertStatus(401)
            ->assertJsonStructure(['error']);
    }

    public function test_login_with_success() {
        $password = $this->faker->password(8);

        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make($password),
        ];

        $user = User::create($userData);

        $formData = [
            'email' => $user->email,
            'password' => $password,
            'device_name' => "mac"
        ];

        $response = $this->postJson('/api/auth/login', $formData);

        $this->assertDatabaseHas('users', $userData);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['token', 'email', 'created_at', 'name'])
            ->assertJson(['email' => $user->email]);
    }
}
