<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    public function index(): JsonResponse
    {
        $cities = City::orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $cities,
        ]);
    }
}
