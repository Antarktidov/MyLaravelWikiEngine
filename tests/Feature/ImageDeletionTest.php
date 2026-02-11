<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Models\Image;
use App\Models\Wiki;

class ImageDeletionTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_users_with_image_deletion_global_right_can_delete_images(): void
    {
        Wiki::factory()->create([
            "url" => "commonswiki",
        ]);

        Storage::fake('public');

        $image_name = 'test-content.jpg';

        $image = Image::factory()->create([
            'filename' => $image_name,
        ]);
        Storage::disk('public')->put("images/$image_name", 'fake-content');

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test25@example.com',
        ]);

        $usergoup = UserGroup::factory()->create([
            'name' => 'imagedeletors-global',
            'can_delete_commons_images' => 1,
        ]);

        UserUserGroupWiki::factory()->create([
            'user_id' => $user->id,
            'user_group_id' => $usergoup->id,
            'wiki_id' => 0,
        ]);

        $this->actingAs($user);

        $response = $this->delete("/commons/delete/{$image->id}");

        $response->assertStatus(200);

        Storage::disk('public')->assertExists("images/$image_name");

        $response2 = $this->get("/storage/images/$image_name");

        $response2->assertStatus(403);

        $this->assertSoftDeleted('images', [
            'id' => $image->id,
        ]);
    }
}
