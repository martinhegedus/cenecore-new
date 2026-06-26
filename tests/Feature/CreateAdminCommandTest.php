<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_admin_command_creates_an_admin_user(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Name', 'Admin User')
            ->expectsQuestion('Email', 'admin@example.com')
            ->expectsQuestion('Password', 'secure-password')
            ->expectsOutput('Admin user admin@example.com created.')
            ->assertSuccessful();

        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame('Admin User', $user->name);
        $this->assertTrue($user->is_admin);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('secure-password', $user->password));
    }

    public function test_create_admin_command_promotes_an_existing_user(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'editor@example.com',
            'is_admin' => false,
        ]);

        $this->artisan('app:create-admin')
            ->expectsQuestion('Name', 'Editor Admin')
            ->expectsQuestion('Email', 'editor@example.com')
            ->expectsQuestion('Password', 'new-secure-password')
            ->expectsOutput('Admin access granted to editor@example.com.')
            ->assertSuccessful();

        $user->refresh();

        $this->assertSame('Editor Admin', $user->name);
        $this->assertTrue($user->is_admin);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('new-secure-password', $user->password));
    }
}
