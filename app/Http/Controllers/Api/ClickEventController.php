<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClickEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ClickEventController extends Controller
{
    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'events' => 'required|array|max:50',
            'events.*.session_id' => 'nullable|string|max:64',
            'events.*.page_url' => 'required|string|max:2000',
            'events.*.route_name' => 'nullable|string|max:255',
            'events.*.element_tag' => 'nullable|string|max:64',
            'events.*.element_id' => 'nullable|string|max:191',
            'events.*.element_classes' => 'nullable|string|max:2000',
            'events.*.element_text' => 'nullable|string|max:2000',
            'events.*.element_name' => 'nullable|string|max:191',
            'events.*.element_type' => 'nullable|string|max:64',
            'events.*.input_value' => 'nullable|string|max:2000',
            'events.*.click_x' => 'nullable|integer|min:0',
            'events.*.click_y' => 'nullable|integer|min:0',
            'events.*.occurred_at' => 'nullable|date',
        ]);

        $user = Auth::guard('sanctum')->user();
        $now = now();
        $userAgent = $request->userAgent();
        $ipAddress = $request->ip();
        $records = [];

        foreach ($validated['events'] as $event) {
            $records[] = [
                'user_id' => $user?->id,
                'session_id' => $event['session_id'] ?? null,
                'page_url' => $event['page_url'],
                'route_name' => $event['route_name'] ?? null,
                'element_tag' => $event['element_tag'] ?? null,
                'element_id' => $event['element_id'] ?? null,
                'element_classes' => $event['element_classes'] ?? null,
                'element_text' => $event['element_text'] ?? null,
                'element_name' => $event['element_name'] ?? null,
                'element_type' => $event['element_type'] ?? null,
                'input_value' => $event['input_value'] ?? null,
                'click_x' => $event['click_x'] ?? null,
                'click_y' => $event['click_y'] ?? null,
                'occurred_at' => isset($event['occurred_at']) ? Carbon::parse($event['occurred_at']) : null,
                'user_agent' => $userAgent,
                'ip_address' => $ipAddress,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($records)) {
            ClickEvent::insert($records);
        }

        return response()->json([
            'status' => 'ok',
            'count' => count($records),
        ]);
    }
}
