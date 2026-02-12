<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::query()
            ->where('is_active', true)
            ->withCount(['photographers' => function ($query) {
                $query->publicVisible();
            }]);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        $locations = $query
            ->select('id', 'name', 'slug', 'type', 'parent_id', 'sort_order')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $locations,
        ]);
    }
}
