<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Wiki;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

class DeleteUnexistingRevisionTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_deletion_unexisting_revision_will_return_404(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test29@example.com',
        ]);

        $managed_user = User::factory()->create([
            'name' => 'Test User2',
            'email' => 'test206@example.com',
        ]);
        $usergroup = UserGroup::factory()->create([
            'name' => 'revisor-global',
            'can_delete_revisions' => 1,
            'is_global' => 1,
        ]);


        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergroup->id,
            'wiki_id' => 0,
        ]);

        $wikiName = "no_such_wiki";
        $articleName = "no_such_article";
        $revisionId = "2222";

        $this->actingAs($user);

        $response = $this->delete("/wiki/{$wikiName}/{$articleName}/{$revisionId}/destroy");

        $response->assertStatus(404);
    }
}

