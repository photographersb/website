<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ApiResponse;
use App\Models\Photographer;
use App\Models\PhotographerFavorite;
use Illuminate\Http\Request;

class ClientFavoritesController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user();
        $role = strtolower((string) ($user->role ?? ''));

        if ($role !== 'client') {
            return $this->unauthorized('Client access required');
        }

        $favorites = PhotographerFavorite::where('user_id', $user->id)
            ->with(['photographer.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($favorites, 'Favorites retrieved successfully');
    }

    public function store(Request $request, Photographer $photographer)
    {
        $user = $request->user();
        $role = strtolower((string) ($user->role ?? ''));

        if ($role !== 'client') {
            return $this->unauthorized('Client access required');
        }

        $favorite = PhotographerFavorite::firstOrCreate([
            'user_id' => $user->id,
            'photographer_id' => $photographer->id,
        ]);

        return $this->created($favorite, 'Photographer added to favorites');
    }

    public function destroy(Request $request, Photographer $photographer)
    {
        $user = $request->user();
        $role = strtolower((string) ($user->role ?? ''));

        if ($role !== 'client') {
            return $this->unauthorized('Client access required');
        }

        PhotographerFavorite::where('user_id', $user->id)
            ->where('photographer_id', $photographer->id)
            ->delete();

        return $this->success([], 'Photographer removed from favorites');
    }
}
