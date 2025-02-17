<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_max_length()
    {
        $longContent = str_repeat('a', 65536);

        $document = new Document([
            'category_id' => 1,
            'title' => 'Test Title',
            'contents' => $longContent,
        ]);

        $this->assertFalse($document->save(), 'Document longer than 65535 characters');
    }
}
