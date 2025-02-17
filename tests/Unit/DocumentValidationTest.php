<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Document;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class DocumentValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_remessa_title_contains_semestre()
    {
        $this->expectException(ValidationException::class);

        $category = Category::create(['name' => 'Remessa']);

        $document = new Document([
            'category_id' => $category->id,
            'title' => 'Test Title',
            'contents' => 'Test Content',
        ]);

        $document->save();
    }

    public function test_remessa_parcial_title_contains_month()
    {
        $this->expectException(ValidationException::class);

        $category = Category::create(['name' => 'Remessa Parcial']);

        $document = new Document([
            'category_id' => $category->id,
            'title' => 'Test Title',
            'contents' => 'Test Content',
        ]);

        $document->save();
    }
}
