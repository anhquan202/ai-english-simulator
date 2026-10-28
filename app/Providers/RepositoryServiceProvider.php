<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\DialogueTurnRepositoryInterface as AltDialogueTurnRepositoryInterface;
use App\Contracts\Repositories\PracticeSessionRepositoryInterface as AltPracticeSessionRepositoryInterface;
use App\Contracts\Repositories\ScenarioRepositoryInterface as AltScenarioRepositoryInterface;
use App\Contracts\Services\AiSimulatorServiceInterface as AltAiSimulatorServiceInterface;
use App\Repositories\Contracts\DialogueTurnRepositoryInterface;
use App\Repositories\Contracts\PracticeSessionRepositoryInterface;
use App\Repositories\Contracts\ScenarioRepositoryInterface;
use App\Repositories\Eloquent\DialogueTurnRepository;
use App\Repositories\Eloquent\PracticeSessionRepository;
use App\Repositories\Eloquent\ScenarioRepository;
use App\Services\Contracts\AiSimulatorServiceInterface;
use App\Services\GeminiSimulatorService;
use App\Services\OpenRouterSimulatorService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All interface-to-implementation repository bindings.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        // Primary Repository Contracts
        ScenarioRepositoryInterface::class => ScenarioRepository::class,
        PracticeSessionRepositoryInterface::class => PracticeSessionRepository::class,
        DialogueTurnRepositoryInterface::class => DialogueTurnRepository::class,

        // Alternative namespace aliases
        AltScenarioRepositoryInterface::class => ScenarioRepository::class,
        AltPracticeSessionRepositoryInterface::class => PracticeSessionRepository::class,
        AltDialogueTurnRepositoryInterface::class => DialogueTurnRepository::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $provider = config('services.ai_provider', 'openrouter');

        $driverClass = ($provider === 'gemini')
            ? GeminiSimulatorService::class
            : OpenRouterSimulatorService::class;

        $this->app->singleton(AiSimulatorServiceInterface::class, $driverClass);
        $this->app->singleton(AltAiSimulatorServiceInterface::class, $driverClass);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
