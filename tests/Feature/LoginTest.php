<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @author Yakubu Alhassan <yaqoubdramani@gmail.com>
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    public function testUserLoginWithValidCredentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $response = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        // dd($response);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'token_type',
                'token_expiration',
                'email_verified',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                ],
            ]);
    }

    /**
     * @test
     */
    public function testUserLoginWithInvalidCredentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'invalid_password',
        ]);

        // Assert response status is 401 Unauthorized
        $response->assertStatus(401)
            // Assert response contains error message
            ->assertJson([
                'error' => 'Invalid login credentials',
            ]);
    }
}
