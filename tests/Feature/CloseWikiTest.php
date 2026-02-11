<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

class CloseWikiTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_steward_can_close_wikis(): void
    {
        $wiki = Wiki::factory()->create([
            'url' => 'wiki_for_closing',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test41@example.com',
        ]);

        $usergoup = UserGroup::factory()->create([
            'name' => 'wiki_closers',
            'can_close_wikis' => 1,
        ]);

        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergoup->id,
            'wiki_id' => 0,
        ]);

        $this->actingAs($user);

        $response = $this->delete("/destroy/{$wiki->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('wikis', [
            'id' => $wiki->id,
        ]);
    }
}
