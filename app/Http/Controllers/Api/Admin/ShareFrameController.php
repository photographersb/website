<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShareFrame;
use App\Models\Competition;
use Illuminate\Http\Request;

class ShareFrameController extends Controller
{
    public function index(Request $request)
    {
        $query = ShareFrame::with('competition');
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }
        
        if ($request->has('competition_id')) {
            $query->where('competition_id', $request->input('competition_id'));
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        
        $frames = $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 20));
        
        return response()->json([
            'data' => $frames->items(),
            'pagination' => [
                'total' => $frames->total(),
                'per_page' => $frames->perPage(),
                'current_page' => $frames->currentPage(),
                'last_page' => $frames->lastPage(),
            ]
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'competition_id' => 'required|exists:competitions,id',
            'background_color' => 'sometimes|string|max:7',
            'text_color' => 'sometimes|string|max:7',
            'accent_color' => 'sometimes|string|max:7',
            'font_family' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);
        
        $frame = ShareFrame::create($validated);
        
        return response()->json(['data' => $frame->load('competition')], 201);
    }
    
    public function show($id)
    {
        $frame = ShareFrame::with('competition')->findOrFail($id);
        return response()->json(['data' => $frame]);
    }
    
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'competition_id' => 'sometimes|exists:competitions,id',
            'background_color' => 'sometimes|string|max:7',
            'text_color' => 'sometimes|string|max:7',
            'accent_color' => 'sometimes|string|max:7',
            'font_family' => 'sometimes|string|max:255',
            'cta_message' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
        ]);
        
        $frame = ShareFrame::findOrFail($id);
        $frame->update($validated);
        
        return response()->json(['data' => $frame->load('competition')]);
    }
    
    public function destroy($id)
    {
        $frame = ShareFrame::findOrFail($id);
        $frame->delete();
        
        return response()->json(null, 204);
    }
}
