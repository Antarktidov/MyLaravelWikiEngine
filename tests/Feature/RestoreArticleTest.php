<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;
use App\Models\Wiki;
use App\Models\Article;
use App\Models\Revision;

class RestoreArticleTest extends TestCase
{   
    use RefreshDatabase;
    public function test_that_user_with_restore_articles_right_can_restore_article(): void
    {
        $wiki = Wiki::factory()->create([
            'url' => 'wiki-for-article-creation-test',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test5@example.com',
        ]);

        $usergroup = UserGroup::factory()->create([
            'name' => 'article_restorer',
            'can_restore_articles' => 1,
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

        $response = $this->post("/wiki/{$wiki->url}/{$article->url_title}/restore");

        $response->assertStatus(200);

        $this->assertDatabaseHas('articles', [
            'wiki_id' => $wiki->id,
            'url_title' => $article->url_title,
            'title' => $article->title,
            'deleted_at' => null,
        ]);
    }
}
