<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\Article;
use App\Models\User;
use App\Models\Comment;
use App\Models\CommentRevision;

class ArticleCommentCRevisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_model_creation(): void
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

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_comment_revision_model_creation(): void
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

        $this->assertDatabaseHas('comment_revisions', [
            'id' => $comment_revision->id,
            'comment_id' => $comment->id,
        ]);
    }

    public function test_article_has_many_comments_relationship(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create(['wiki_id' => $wiki->id]);
        $user = User::factory()->create();
        $comment1 = Comment::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
        ]);
        $comment2 = Comment::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
        ]);

        $this->assertCount(2, $article->comments);
        $this->assertTrue($article->comments->contains($comment1));
        $this->assertTrue($article->comments->contains($comment2));
    }

    public function test_comment_belongs_to_article_relationship(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create(['wiki_id' => $wiki->id]);
        $user = User::factory()->create();
        $comment = Comment::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
        ]);

        $this->assertEquals($article->id, $comment->article->id);
        $this->assertInstanceOf(Article::class, $comment->article);
    }

    public function test_comment_belongs_to_user_relationship(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create(['wiki_id' => $wiki->id]);
        $user = User::factory()->create();
        $comment = Comment::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
        ]);

        $this->assertEquals($user->id, $comment->user->id);
        $this->assertInstanceOf(User::class, $comment->user);
    }

    public function test_comment_has_many_comment_revisions_relationship(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create(['wiki_id' => $wiki->id]);
        $user = User::factory()->create();
        $comment = Comment::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
        ]);
        $comment_revision1 = CommentRevision::factory()->create([
            'comment_id' => $comment->id,
            'user_ip' => '127.0.0.1',
        ]);
        $comment_revision2 = CommentRevision::factory()->create([
            'comment_id' => $comment->id,
            'user_ip' => '127.0.0.1',
        ]);

        $this->assertCount(2, $comment->comment_revisions);
        $this->assertTrue($comment->comment_revisions->contains($comment_revision1));
        $this->assertTrue($comment->comment_revisions->contains($comment_revision2));
    }

    public function test_comment_revision_belongs_to_comment_relationship(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create(['wiki_id' => $wiki->id]);
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

        $this->assertEquals($comment->id, $comment_revision->comment->id);
        $this->assertInstanceOf(Comment::class, $comment_revision->comment);
    }
}
