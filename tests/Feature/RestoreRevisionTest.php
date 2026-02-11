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

class RestoreRevisionTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_user_with_restore_revision_rights_can_restore_revisions(): void
    {
        $wiki = Wiki::factory()->create([
            'url' => 'wiki-for-article-deletion-test',
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
            'is_approved' => true,
            'is_patrolled' => true,
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test3@example.com',
        ]);

        $usergroup = UserGroup::factory()->create([
            'name' => 'revision_restorer',
            'can_restore_revisions' => 1,
            'is_global' => 0,
        ]);

        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergroup->id,
            'wiki_id' => $wiki->id,
        ]);

        $this->actingAs($user);

        $response = $this->post("/wiki/{$wiki->url}/{$article->url_title}/{$revision->id}/restore");

        $response->assertStatus(200);

        $this->assertDatabaseHas('revisions', [
            'article_id' => $article->id,
            'url_title' => $revision->url_title,
            'title' => $revision->title,
            'deleted_at' => null,
        ]);
    }
}
