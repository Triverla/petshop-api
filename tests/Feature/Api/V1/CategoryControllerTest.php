<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Base;

class CategoryControllerTest extends Base
{
    use RefreshDatabase;

    public function testCanGetCategories(): void
    {
        $response = $this->get('api/v1/categories');

        $response->assertStatus(200);
    }

    public function testCanShowCategory()
    {
        $category = Category::factory()->create();
        $response = $this->get("api/v1/category/{$category->uuid}");
        $response->assertStatus(200);
    }

    public function testCanCreateCategory()
    {
        $this->actingAs($this->buckHillAdmin)->postJson(
            "api/v1/category/create",
            ['title' => 'Test'],
            [
                'Accept' => "application/json",
                'Authorization' => "Bearer $this->jwtAdminToken"
            ]
        )->assertStatus(201);
    }

    public function testCanUpdateCategory()
    {
        $category = Category::factory()->create();
        $this->put(
            "api/v1/category/{$category->uuid}",
            [
                'title' => "Updated title",
            ],
            [
                'Accept' => "application/json",
                'Authorization' => "Bearer $this->jwtAdminToken"
            ]
        )->assertStatus(200);
        self::assertEquals('Updated title', Category::find($category)->first()->title);
    }
}
