<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Scenario;
use App\Models\UserFavorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FavoriteController extends Controller
{
    /**
     * Toggle favorite/pin status of a scenario for an authenticated user or guest
     */
    public function toggle(int $scenarioId, Request $request): JsonResponse
    {
        $scenario = Scenario::where('id', $scenarioId)->where('is_active', true)->first();

        if (! $scenario) {
            return response()->json([
                'success' => false,
                'message' => 'Scenario not found.',
            ], 404);
        }

        $userId = auth()->id();
        $guestToken = (string) ($request->input('guest_token') ?? $request->cookie('guest_token') ?? '');
        $isNewGuestToken = false;

        if (! $userId && empty($guestToken)) {
            $guestToken = (string) Str::uuid();
            $isNewGuestToken = true;
        }

        $query = UserFavorite::where('scenario_id', $scenarioId);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('guest_token', $guestToken);
        }

        $existing = $query->first();

        if ($existing) {
            $existing->delete();
            $isFavorited = false;
        } else {
            UserFavorite::create([
                'user_id' => $userId,
                'guest_token' => $userId ? null : $guestToken,
                'scenario_id' => $scenarioId,
            ]);
            $isFavorited = true;
        }

        $response = response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'scenario_id' => $scenarioId,
        ]);

        if ($isNewGuestToken) {
            $response->cookie('guest_token', $guestToken, 60 * 24 * 30);
        }

        return $response;
    }
}
