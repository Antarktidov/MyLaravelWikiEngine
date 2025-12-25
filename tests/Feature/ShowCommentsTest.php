<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\Article;
use App\Models\User;
use App\Models\Comment;
use App\Models\CommentRevision;

class ShowCommentsTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_unauthorized_user_can_asses_comments_via_api(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create([
            'wiki_id' => $wiki->id,
        ]);
        $user = User::factory()->create();
        $comment = Comment::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
        ]);
        $comment_revision = CommentRevision::factory()->create([
            'comment_id' => $comment->id,
            'user_ip' => '127.0.0.1',
        ]);

        $response = $this->get("/api/wiki/{$wiki->url}/article/{$article->url_title}/comments");

        $response->assertStatus(200);
    }
}
