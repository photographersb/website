<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaUploadController extends Controller
{
    use ApiResponse;

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:5120',
            'folder' => 'nullable|string|max:100',
        ]);

        $folder = trim($validated['folder'] ?? 'competitions');
        $folder = preg_replace('/[^a-zA-Z0-9_-]/', '', $folder) ?: 'competitions';

        $path = $request->file('image')->store("uploads/{$folder}", 'public');
        $url = asset('storage/' . $path);

        return $this->success([
            'url' => $url,
            'path' => $path,
        ], 'Image uploaded successfully');
    }
}
