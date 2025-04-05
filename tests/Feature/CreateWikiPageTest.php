<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

class CreateWikiPageTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_unauthorized_user_can_not_asses_to_create_wiki_page(): void
    {
        $response = $this->get('/create-wiki');

        $response->assertStatus(401);
    }

    public function test_that_authorized_user_without_special_permissions_can_not_asses_to_create_wiki_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->actingAs($user);

        $response = $this->get('/create-wiki');

        $response->assertStatus(403);
    }

    public function test_that_authorized_user_with_create_wiki_permission_can_asses_to_create_wiki_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $usergoup =  UserGroup::factory()->create([
            'name' => 'wikicreators-global',
            'can_create_wikis' => 1,
        ]);

        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergoup->id,
            'wiki_id' => 0,
        ]);

        $this->actingAs($user);

        $response = $this->get('/create-wiki');

        $response->assertStatus(200);
    }
}
