<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

class WikiCreationTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_unauthorized_user_can_not_crete_wikis(): void
    {
        $data = [
            'url' => 'TestWiki',
        ];

        $response = $this->post('/store', $data);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('wikis', [
            'url' => $data['url'],
        ]);
    }

    public function test_that_authorized_user_without_special_rights_can_not_create_wikis(): void
    {

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $data = [
            'url' => 'TestWiki',
        ];

        $this->actingAs($user);

        $response = $this->post('/store', $data);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('wikis', [
            'url' => $data['url'],
        ]);
    }

    public function test_that_authorized_user_with_createwiki_rights_can_create_wikis(): void
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

        $data = [
            'url' => 'TestWiki',
        ];

        $this->actingAs($user);

        $response = $this->post('/store', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('wikis', [
            'url' => $data['url'],
        ]);
    }

    public function test_that_authorized_user_with_local_createwiki_rights_can_not_create_wikis(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $usergoup =  UserGroup::factory()->create([
            'name' => 'wikicreators-local',
            'can_create_wikis' => 1,
        ]);

        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergoup->id,
            'wiki_id' => 1,
        ]);

        $data = [
            'url' => 'TestWiki',
        ];

        $this->actingAs($user);

        $response = $this->post('/store', $data);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('wikis', [
            'url' => $data['url'],
        ]);
    }
}
