<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadsTest extends TestCase
{
    use RefreshDatabase;
    public function test_that_unauthorized_users_can_upload_images(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('wiki-image.jpg', 800, 600);

        $response = $this->post('/commons/store', [
            'image' => $image,
        ]);

        Storage::disk('public')->assertExists("images/{$image->hashName()}");

        $this->assertDatabaseHas('images', [
            'filename' => $image->hashName(),
        ]);

        $response->assertStatus(200);
    }
}
