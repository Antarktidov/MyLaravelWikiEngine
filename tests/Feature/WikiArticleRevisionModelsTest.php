<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\Article;
use App\Models\Revision;
use App\Models\User;

class WikiArticleRevisionModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_wiki_model_creation(): void
    {
        $wiki = Wiki::factory()->create();

        $this->assertDatabaseHas('wikis', [
            'id' => $wiki->id,
        ]);
    }

    public function test_article_model_creation(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create([
            'wiki_id' => $wiki->id,
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'wiki_id' => $wiki->id,
        ]);
    }

    public function test_revision_model_creation(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create([
            'wiki_id' => $wiki->id,
        ]);
        $user = User::factory()->create();
        $revision = Revision::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
        ]);

        $this->assertDatabaseHas('revisions', [
            'id' => $revision->id,
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_wiki_has_many_articles_relationship(): void
    {
        $wiki = Wiki::factory()->create();
        $article1 = Article::factory()->create(['wiki_id' => $wiki->id]);
        $article2 = Article::factory()->create(['wiki_id' => $wiki->id]);

        $this->assertCount(2, $wiki->articles);
        $this->assertTrue($wiki->articles->contains($article1));
        $this->assertTrue($wiki->articles->contains($article2));
    }

    public function test_article_belongs_to_wiki_relationship(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create([
            'wiki_id' => $wiki->id,
        ]);

        $this->assertEquals($wiki->id, $article->wiki->id);
        $this->assertInstanceOf(Wiki::class, $article->wiki);
    }

    public function test_article_has_many_revisions_relationship(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create(['wiki_id' => $wiki->id]);
        $user = User::factory()->create();
        $revision1 = Revision::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
        ]);
        $revision2 = Revision::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
        ]);

        $this->assertCount(2, $article->revisions);
        $this->assertTrue($article->revisions->contains($revision1));
        $this->assertTrue($article->revisions->contains($revision2));
    }

    public function test_revision_belongs_to_article_relationship(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create(['wiki_id' => $wiki->id]);
        $user = User::factory()->create();
        $revision = Revision::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
        ]);

        $this->assertEquals($article->id, $revision->article->id);
        $this->assertInstanceOf(Article::class, $revision->article);
    }

    public function test_revision_belongs_to_user_relationship(): void
    {
        $wiki = Wiki::factory()->create();
        $article = Article::factory()->create(['wiki_id' => $wiki->id]);
        $user = User::factory()->create();
        $revision = Revision::factory()->create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'user_ip' => '127.0.0.1',
        ]);

        $this->assertEquals($user->id, $revision->user->id);
        $this->assertInstanceOf(User::class, $revision->user);
    }
}
