<?php

namespace Tests\Feature;

use App\Support\Locale;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this
            ->withCookie(Locale::COOKIE_NAME, 'invalid')
            ->withHeader('Accept-Language', '')
            ->get('/');

        $response->assertRedirect('/sk');
    }
}
