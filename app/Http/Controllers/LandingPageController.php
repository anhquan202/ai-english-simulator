<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Contracts\ScenarioRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __construct(
        protected ScenarioRepositoryInterface $scenarioRepository
    ) {}

    /**
     * Display the public dedicated Landing Page.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $guestToken = (string) ($request->cookie('guest_token') ?? $request->query('guest_token') ?? '');

        $scenarios = $this->scenarioRepository->getActiveScenarios(
            userId: $userId,
            guestToken: $guestToken ?: null
        );

        $heroSpotlight = $this->scenarioRepository->getHeroSpotlight($userId, $guestToken ?: null);

        return view('landing.index', compact('scenarios', 'heroSpotlight'));
    }
}
