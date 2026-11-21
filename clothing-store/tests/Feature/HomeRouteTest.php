<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class HomeRouteTest extends TestCase
{
    public function test_home_route_returns_200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_home_route_contains_brand_name(): void
    {
        $response = $this->get('/');
        $response->assertSee('{BRAND}');
    }

    public function test_admin_route_redirects_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_login_page_accessible(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }
}
