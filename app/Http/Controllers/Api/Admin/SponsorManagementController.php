<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorManagementController extends Controller
{
    /**
     * Get all sponsors with pagination and filters
     */
    public function index(Request $request)
    {
        $query = Sponsor::query();
        
        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        $sponsors = $query->paginate($request->input('per_page', 10));
        
        return response()->json([
            'data' => $sponsors->items(),
            'pagination' => [
                'total' => $sponsors->total(),
                'per_page' => $sponsors->perPage(),
                'current_page' => $sponsors->currentPage(),
                'last_page' => $sponsors->lastPage(),
            ]
        ]);
    }
    
    /**
     * Get a single sponsor
     */
    public function show(Sponsor $sponsor)
    {
        return response()->json(['data' => $sponsor]);
    }
    
    /**
     * Create a new sponsor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sponsors',
            'category' => 'required|string|max:100',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'required|email|unique:sponsors',
            'contact_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'logo_url' => 'nullable|url',
            'description' => 'nullable|string',
        ]);
        
        $sponsor = Sponsor::create([
            ...$validated,
            'status' => 'active',
        ]);
        
        return response()->json(['data' => $sponsor], 201);
    }
    
    /**
     * Update sponsor
     */
    public function update(Request $request, Sponsor $sponsor)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255|unique:sponsors,name,' . $sponsor->id,
            'category' => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|unique:sponsors,contact_email,' . $sponsor->id,
            'contact_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'logo_url' => 'nullable|url',
            'description' => 'nullable|string',
        ]);
        
        $sponsor->update($validated);
        
        return response()->json(['data' => $sponsor]);
    }
    
    /**
     * Toggle sponsor status
     */
    public function toggleStatus(Sponsor $sponsor)
    {
        $sponsor->status = $sponsor->status === 'active' ? 'inactive' : 'active';
        $sponsor->save();
        
        return response()->json(['data' => $sponsor]);
    }
    
    /**
     * Delete sponsor
     */
    public function destroy(Sponsor $sponsor)
    {
        $sponsor->delete();
        
        return response()->json(null, 204);
    }
}
