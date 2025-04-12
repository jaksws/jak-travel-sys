<?php

namespace Tests\Unit;

use App\Models\Document;
use App\Models\User;
use App\Models\Request;
use App\Models\Quote;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_document()
    {
        $user = User::factory()->create();
        $request = Request::factory()->create();
        
        $document = Document::factory()->create([
            'name' => 'جواز سفر.pdf',
            'file_path' => 'documents/passports/passport123.pdf',
            'file_type' => 'application/pdf',
            'size' => 1024 * 800, // 800 KB
            'documentable_id' => $request->id,
            'documentable_type' => Request::class,
            'uploaded_by' => $user->id,
            'visibility' => 'agency',
            'notes' => 'نسخة من جواز سفر العميل'
        ]);

        $this->assertDatabaseHas('documents', [
            'name' => 'جواز سفر.pdf',
            'file_path' => 'documents/passports/passport123.pdf',
            'documentable_id' => $request->id,
            'documentable_type' => Request::class,
            'uploaded_by' => $user->id,
        ]);
    }

    #[Test]
    public function it_has_documentable_relationship()
    {
        $request = Request::factory()->create();
        
        $document = Document::factory()->create([
            'documentable_id' => $request->id,
            'documentable_type' => Request::class,
            'size' => 1024 * 100, // 100 KB
        ]);

        $this->assertEquals($request->id, $document->documentable->id);
        $this->assertInstanceOf(Request::class, $document->documentable);
    }
    
    #[Test]
    public function it_belongs_to_uploader()
    {
        $user = User::factory()->create();
        
        $document = Document::factory()->create([
            'uploaded_by' => $user->id,
            'size' => 1024 * 50, // 50 KB
        ]);

        $this->assertEquals($user->id, $document->uploader->id);
        $this->assertInstanceOf(User::class, $document->uploader);
    }
    
    #[Test]
    public function it_generates_file_url()
    {
        $document = Document::factory()->create([
            'file_path' => 'documents/contracts/contract123.pdf',
            'size' => 1024 * 300, // 300 KB
        ]);
        
        // Mock the Storage facade
        Storage::shouldReceive('url')
               ->with('documents/contracts/contract123.pdf')
               ->andReturn('http://example.com/storage/documents/contracts/contract123.pdf');
        
        $this->assertEquals('http://example.com/storage/documents/contracts/contract123.pdf', $document->file_url);
    }
    
    #[Test]
    public function it_can_format_file_size()
    {
        $smallDoc = Document::factory()->create([
            'size' => 800, // 800 bytes
        ]);
        
        $mediumDoc = Document::factory()->create([
            'size' => 1024 * 800, // 800 KB
        ]);
        
        $largeDoc = Document::factory()->create([
            'size' => 1024 * 1024 * 3.5, // 3.5 MB
        ]);
        
        $this->assertEquals('800 bytes', $smallDoc->formatted_size);
        $this->assertEquals('800.00 KB', $mediumDoc->formatted_size);
        $this->assertEquals('3.50 MB', $largeDoc->formatted_size);
    }
    
    #[Test]
    public function it_can_attach_to_different_models()
    {
        $request = Request::factory()->create();
        $quote = Quote::factory()->create();
        
        $requestDocument = Document::factory()->create([
            'documentable_id' => $request->id,
            'documentable_type' => Request::class,
            'name' => 'وثيقة للطلب',
            'size' => 1024 * 200,
        ]);
        
        $quoteDocument = Document::factory()->create([
            'documentable_id' => $quote->id,
            'documentable_type' => Quote::class,
            'name' => 'وثيقة لعرض السعر',
            'size' => 1024 * 150,
        ]);
        
        $this->assertInstanceOf(Request::class, $requestDocument->documentable);
        $this->assertInstanceOf(Quote::class, $quoteDocument->documentable);
        
        $this->assertEquals($request->id, $requestDocument->documentable->id);
        $this->assertEquals($quote->id, $quoteDocument->documentable->id);
    }
}