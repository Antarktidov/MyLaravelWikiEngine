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

class ViewDeletedRevisionsTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_user_with_view_deleted_revisions_right_can_view_deleted_revisions(): void
    {
        $wiki = Wiki::factory()->create([
            'url' => 'mywiki',
        ]);

        $article = Article::factory()->create([
            'wiki_id' => $wiki->id,
        ]);

        $revision = Revision::factory()->create([
            'article_id' => $article->id,
            'url_title' => $article->url_title,
            'title' => $article->title,
            'user_id' => 0,
            'user_ip' => '127.0.0.1',
            'deleted_at' => '2025-12-01 21:55:38',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $usergroup = UserGroup::factory()->create([
            'name' => 'deleted_history_viewer',
            'can_view_deleted_revisions' => 1,
            'is_global' => 0,
        ]);

        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergroup->id,
            'wiki_id' => $wiki->id,
        ]);

        $this->actingAs($user);

        $response = $this->get("/wiki/{$wiki->url}/article/{$article->url_title}/deleted_history");

        $response->assertStatus(200);
    }
}
