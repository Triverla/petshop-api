<?php

namespace Tests\Feature\Api\V1;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Base;

class FileTest extends Base
{
    public function testCanUploadFile(): void
    {
        $this->actingAs($this->user)->post('/api/v1/file/upload', [
            'file' => UploadedFile::fake()->image('test.png'),
        ], [
            'Accept' => "application/json",
            'Authorization' => 'Bearer ' . $this->jwtUserToken
        ])->assertOk();
    }

    public function testCanGetImage()
    {
        $image = UploadedFile::fake()->image('test.jpg');
        $filepath = Storage::putFileAs('/pet-shop', $image, 'test.jpg');
        $uuid = Str::uuid();
        $file = File::factory()->create(
            [
                'uuid' => $uuid,
                'name' => $image->getFilename(),
                'path' => $filepath,
                'type' => $image->getMimeType(),
            ]
        );

        $this->assertTrue(Storage::disk('local')->exists('pet-shop/test.jpg'));
        $response =   $this->actingAs($this->user)->get("/api/v1/file/$file->uuid", [
            'Authorization' => 'Bearer ' . $this->jwtUserToken
        ])->assertOk();;
        $response->assertHeader('Content-Type', 'image/jpeg');
    }
}
