<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\Article;
use App\Models\Revision;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

use App\Models\Comment;
use App\Models\CommentRevision;

class DeleteCommentsTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_user_with_delete_comments_right_can_delete_comments(): void
    {
        $wiki = Wiki::factory()->create([
            'url' => 'wiki-for-comments-test',
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
            'email' => 'test@example.com',
        ]);

        $usergroup = UserGroup::factory()->create([
            'name' => 'comments-deletor',
            'can_delete_comments' => 1,
            'is_global' => 0,
        ]);

        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergroup->id,
            'wiki_id' => $wiki->id,
        ]);

        $comment = Comment::factory()->create([
            'article_id' => $article->id,
            'user_id' => 0,
            'user_ip' => '127.0.0.1',
        ]);

        $comment_revision = CommentRevision::factory()->create([
            'user_ip' => '127.0.0.1',
            'comment_id' => $comment->id,
        ]);

        $this->actingAs($user);

        $response = $this->delete("/api/wiki/{$wiki->url}/article/{$article->url_title}/comments/{$comment->id}/delete");

        $response->assertStatus(200);

        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);
    }
}
