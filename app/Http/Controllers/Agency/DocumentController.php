<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'name' => 'nullable|string|max:255',
            'related_id' => 'required|integer',
            'related_type' => 'required|string',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $name = $request->input('name', $originalName);
        $extension = $file->getClientOriginalExtension();
        
        // Generate a unique filename
        $filename = Str::uuid() . '.' . $extension;
        
        // Store the file
        $path = $file->storeAs('documents', $filename, 'private');
        
        // Create document record
        $document = new Document();
        $document->name = $name;
        $document->original_name = $originalName;
        $document->file_path = $path;
        $document->file_size = $file->getSize();
        $document->file_type = $file->getMimeType();
        $document->related_id = $request->input('related_id');
        $document->related_type = $request->input('related_type');
        $document->user_id = auth()->id();
        $document->agency_id = auth()->user()->agency_id;
        $document->save();
        
        return response()->json([
            'success' => true,
            'document' => $document
        ]);
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Document $document)
    {
        // Check if user has permission
        if ($document->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح بحذف هذا الملف');
        }
        
        // Delete file from storage
        if (Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }
        
        // Delete record
        $document->delete();
        
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Download a document
     */
    public function download(Document $document)
    {
        // Check if user has permission
        if ($document->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح بتحميل هذا الملف');
        }
        
        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'الملف غير موجود');
        }
        
        return Storage::disk('private')->download(
            $document->file_path,
            $document->original_name
        );
    }
}