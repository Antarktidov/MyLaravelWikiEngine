<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

class GlobalUserGroupsTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_users_with_global_manage_global_user_rights_permission_can_assign_global_groups(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $managed_user = User::factory()->create([
            'name' => 'Test User2',
            'email' => 'test2@example.com',
        ]);
        $usergroup = UserGroup::factory()->create([
            'name' => 'global-userrights-managers-global',
            'can_manage_global_userrights' => 1,
            'is_global' => 1,
        ]);

        $assignable_usergroup = UserGroup::factory()->create([
            'name' => 'assignable_group',
            'is_global' => 1,
        ]);


        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergroup->id,
            'wiki_id' => 0,
        ]);

        $globalGroupsIds = [$assignable_usergroup->id];

        $this->actingAs($user);

        $response = $this->post("/global-user-rights/{$managed_user->id}/store", [
            'user_group_ids' => $globalGroupsIds,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_user_group_wiki', [
            'user_id' => $managed_user->id,
            'user_group_id' => $assignable_usergroup->id,
            'wiki_id' => 0,
        ]);

        $response->assertStatus(200);
    }
}
