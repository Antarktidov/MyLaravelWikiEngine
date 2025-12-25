<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\Article;
use App\Models\User;
use App\Models\Revision;

class CreteArticlePageTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_unauthorized_user_can_asses_create_article_page_page(): void
    {
        $wiki = Wiki::factory()->create();

        $response = $this->get("/wiki/{$wiki->url}/all-articles");

        $response->assertStatus(200);
    }
}
