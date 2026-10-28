<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Communication Success Index (CSI) Component Weights
    |--------------------------------------------------------------------------
    | Formula: CSI = (Resolution * 0.40) + (Adaptability * 0.35) + (Flow * 0.25)
    | Total sum of all weights must equal 1.0.
    */
    'csi_weights' => [
        'resolution' => (float) env('SIMULATOR_CSI_WEIGHT_RESOLUTION', 0.40),
        'adaptability' => (float) env('SIMULATOR_CSI_WEIGHT_ADAPTABILITY', 0.35),
        'flow' => (float) env('SIMULATOR_CSI_WEIGHT_FLOW', 0.25),
    ],

    /*
    |--------------------------------------------------------------------------
    | Word Score Thresholds (Pronunciation & Content Analysis)
    |--------------------------------------------------------------------------
    | - green: >= 85 (High accuracy)
    | - orange: >= 60 and < 85 (Acceptable / Intelligible)
    | - red: < 60 (Distorted / Dropped ending sounds)
    */
    'word_score_thresholds' => [
        'green' => (int) env('SIMULATOR_WORD_SCORE_GREEN', 85),
        'orange' => (int) env('SIMULATOR_WORD_SCORE_ORANGE', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Redo (Speak Again) Constraints
    |--------------------------------------------------------------------------
    | Safeguards to prevent audio spam and device lag (BR-EXC-02).
    | - max_attempts: Maximum allowed retries per dialogue turn (default: 2)
    | - cooldown_seconds: Buffer cooldown time before allowing next action (default: 1.5s)
    */
    'redo' => [
        'max_attempts' => (int) env('SIMULATOR_REDO_MAX_ATTEMPTS', 2),
        'cooldown_seconds' => (float) env('SIMULATOR_REDO_COOLDOWN_SECONDS', 1.5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Thinking Window Configuration
    |--------------------------------------------------------------------------
    | Gentle timing window for learners:
    | - duration_seconds: Total generous thinking window before auto-suggestions (15s)
    | - hint_reminder_seconds: Subtle pulse reminder on hint button (8s)
    */
    'thinking_window' => [
        'duration_seconds' => (int) env('SIMULATOR_THINKING_DURATION_SECONDS', 15),
        'hint_reminder_seconds' => (int) env('SIMULATOR_THINKING_HINT_REMINDER_SECONDS', 8),
    ],
];
