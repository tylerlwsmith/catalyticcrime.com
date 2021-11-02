<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    /** @test */
    public function about_page_returns_200()
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
    }
}
