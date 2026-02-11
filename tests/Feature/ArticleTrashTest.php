<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\User;
use App\Models\Article;
use App\Models\Revision;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

class ArticleTrashTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_user_with_view_deleted_articles_right_can_asses_trash_pages(): void
    {
        $wiki = Wiki::factory()->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test42@example.com',
        ]);

        $usergroup = UserGroup::factory()->create([
            'name' => 'arbitrator',
            'can_view_deleted_articles' => 1,
            'is_global' => 0,
        ]);

        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergroup->id,
            'wiki_id' => $wiki->id,
        ]);

        $article = Article::factory()->create([
            'wiki_id' => $wiki->id,
            'deleted_at' => '2025-12-01 21:55:38',
        ]);

        $revision = Revision::factory()->create([
            'article_id' => $article->id,
            'url_title' => $article->url_title,
            'title' => $article->title,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
            'is_approved' => true,
            'is_patrolled' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get("/wiki/{$wiki->url}/trash");

        $response->assertStatus(200);

        $response2 = $this->get("/wiki/{$wiki->url}/trash/article/{$article->url_title}");

        $response2->assertStatus(200);
    }
}
