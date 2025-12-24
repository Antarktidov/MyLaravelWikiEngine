<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\Article;
use App\Models\Revision;

class CreateCommentsTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_unauthorized_user_can_post_comments(): void
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


        $data = [
            'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
        ];

        $response = $this->post("/api/wiki/{$wiki->url}/article/{$article->url_title}/comments/store", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'user_id' => 0,
        ]);

        $this->assertDatabaseHas('comment_revisions', [
            'comment_id' => 1,
            'content' => $data['content'],
        ]);
    }
}
