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

class HideRevisionTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_user_with_delete_revision_tight_can_delete_revision(): void
    {
        $wiki = Wiki::factory()->create([
            'url' => 'wiki-for-revision-test',
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
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test26@example.com',
        ]);

        $usergroup = UserGroup::factory()->create([
            'name' => 'revisor',
            'can_delete_revisions' => 1,
            'is_global' => 0,
        ]);

        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergroup->id,
            'wiki_id' => $wiki->id,
        ]);

        $this->actingAs($user);

        $response = $this->delete("/wiki/{$wiki->url}/{$article->url_title}/{$revision->id}/destroy");

        $response->assertStatus(200);

        $this->assertSoftDeleted('revisions', [
            'id' => $revision->id,
        ]);

        $response2 = $this->get("/wiki/{$wiki->ur}/article/{$article->url_title}");

        $response2->assertStatus(404);
    }
}
