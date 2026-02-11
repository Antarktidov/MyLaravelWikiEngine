<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

class OpenWikiTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_user_with_open_wiki_right_can_open_wiki(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test6@example.com',
        ]);

        $usergroup = UserGroup::factory()->create([
            'name' => 'wiki-opener',
            'can_open_wikis' => 1,
            'is_global' => 1,
        ]);

        $wiki = Wiki::factory()->create([
            'url' => 'closed',
            'deleted_at' => '2025-12-01 21:55:38',
        ]);

        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergroup->id,
            'wiki_id' => 0,
        ]);

        $this->actingAs($user);

        $response = $this->post("/open/{$wiki->id}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('wikis', [
            'url' => $wiki->url,
            'deleted_at' => null,
        ]);
    }
}
