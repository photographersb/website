<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ApiResponse;
use App\Models\Album;
use App\Models\Booking;
use Illuminate\Http\Request;

class ClientAlbumController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user();
        $role = strtolower((string) ($user->role ?? ''));

        if ($role !== 'client') {
            return $this->unauthorized('Client access required');
        }

        $photographerIds = Booking::where('client_id', $user->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->pluck('photographer_id')
            ->unique()
            ->values();

        if ($photographerIds->isEmpty()) {
            return $this->success([], 'No galleries found');
        }

        $albums = Album::whereIn('photographer_id', $photographerIds)
            ->where('is_public', true)
            ->withCount('photos')
            ->with(['photographer.user'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return $this->success($albums, 'Client galleries retrieved successfully');
    }

    public function show(Request $request, Album $album)
    {
        $user = $request->user();
        $role = strtolower((string) ($user->role ?? ''));

        if ($role !== 'client') {
            return $this->unauthorized('Client access required');
        }

        if (!$album->is_public) {
            return $this->notFound('Album not available');
        }

        $canView = Booking::where('client_id', $user->id)
            ->where('photographer_id', $album->photographer_id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        if (!$canView) {
            return $this->unauthorized('You do not have access to this album');
        }

        $album->load([
            'photographer.user',
            'photos' => function ($query) {
                $query->orderBy('display_order')
                    ->orderBy('created_at');
            },
        ]);

        return $this->success($album, 'Album retrieved successfully');
    }
}
