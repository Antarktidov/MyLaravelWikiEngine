<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;
use App\Models\Wiki;

class LocalUserGroupsTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_users_with_local_manage_local_user_rights_permission_can_assign_local_groups(): void
    {
        $wiki = Wiki::factory()->create([
            'url' => 'wiki-for-article-creation-test',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $managed_user = User::factory()->create([
            'name' => 'Test User2',
            'email' => 'test2@example.com',
        ]);
        $usergroup = UserGroup::factory()->create([
            'name' => 'local-userrights-managers-local',
            'can_manage_local_userrights' => 1,
            'is_global' => 0,
        ]);

        $assignable_usergroup = UserGroup::factory()->create([
            'name' => 'assignable_group',
            'is_global' => 0,
        ]);


        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergroup->id,
            'wiki_id' => $wiki->id,
        ]);

        $localGroupsIds = [$assignable_usergroup->id];

        $this->actingAs($user);

        $response = $this->post("/wiki/{$wiki->url}/user-rights/{$managed_user->id}/store", [
            'user_group_ids' => $localGroupsIds,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_user_group_wiki', [
            'user_id' => $managed_user->id,
            'user_group_id' => $assignable_usergroup->id,
            'wiki_id' => $wiki->id,
        ]);
    }
}
