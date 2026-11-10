<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that root renders the welcome page.
     */
    public function test_root_renders_welcome_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('نظام إدارة الشحنات');
    }

    /**
     * Test that login page is accessible.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
