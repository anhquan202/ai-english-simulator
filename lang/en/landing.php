<?php

declare(strict_types=1);

return [
    'page_title' => 'mở lời. - AI Real-World English Simulator',

    // Floating Chapter HUD Tracker
    'hud' => [
        'chapter_1_title' => '01 Real-world Fear',
        'chapter_1_label' => '01. The Freeze Moment',
        'chapter_2_title' => '02 Simulation Experience',
        'chapter_2_label' => '02. Living Simulator Screen',
        'chapter_3_title' => '03 CSI Philosophy',
        'chapter_3_label' => '03. Empathic CSI Score',
        'chapter_4_title' => '04 Try Speaking Now',
        'chapter_4_label' => '04. Try Speaking Now (Micro-Voice)',
    ],

    // Top Navigation
    'nav' => [
        'chapter_1' => '1. Real-world Fear',
        'chapter_2' => '2. Simulation Experience',
        'chapter_3' => '3. CSI Philosophy',
        'chapter_4' => '4. Try Speaking Now',
    ],

    // Chapter 1: The Freeze & Hook
    'chapter_1' => [
        'sticker_left' => '“Could I get a flat white with...”',
        'sticker_right' => '“Take your time, no rush!”',
        'eyebrow_badge' => 'AI REAL-WORLD ENGLISH SIMULATOR',
        'eyebrow_text' => 'Overcome the fear of speaking.',
        'heading_line1' => 'The moment you want to speak...',
        'heading_line2' => 'yet your throat suddenly freezes.',
        'subtitle' => 'You have studied grammar rules for years. Yet standing before a coffee counter in Melbourne or customs at Tokyo Airport, a single unexpected question makes your mind <strong>&ldquo;freeze&rdquo;</strong>.',
        'scenario_badge' => 'REAL-WORLD SITUATION &bull; THE FIRST 3 SECONDS',
        'bubble_ai_speaker' => 'Barista Emma (Melbourne):',
        'bubble_ai_time' => '09:42 AM',
        'bubble_ai_text' => '&ldquo;Hi there! Sorry, we completely ran out of oat milk just now... Would you like soy or almond milk instead?&rdquo;',
        'bubble_user_label' => 'You',
        'bubble_user_thought_header' => 'Racing thoughts inside your head:',
        'bubble_user_thought_text' => '&ldquo;Wait... oat milk is gone, can I switch to soy milk? What was the polite sentence structure? Will they grimace if I say it wrong... Maybe just nod yes...&rdquo;',
        'cause_text' => 'Root cause: <strong>Fear of judgment</strong>, not a lack of vocabulary.',
        'view_solution' => 'See Solution',
    ],

    // Chapter 2: Living Simulator & Stepper
    'chapter_2' => [
        'eyebrow' => 'REAL-WORLD SIMULATION SPACE &bull; 0% JUDGMENT',
        'heading_line1' => 'Simulate a real video call.',
        'heading_line2' => 'Where you are free to make mistakes and retry.',
        'subtitle' => 'No fill-in-the-blank drills. Have 1-on-1 direct conversations with AI inside an authentic video call interface, backed by scaffolding safety nets and a relaxed thinking window.',
        'auto_slide_5s' => 'Auto-slides in 5s',
        'paused_hover' => 'Paused (Hovering)',
        'click_hint' => 'Click cards or arrow buttons to navigate',
        'prev_step' => 'Previous step',
        'next_step' => 'Next step',

        'card_1' => [
            'tag' => 'SAFE SPACE',
            'title' => 'Patient partner &bull; 0% judgment',
            'desc' => 'Barista Emma smiles with natural native intonation, never interrupting or judging your pauses.',
        ],
        'card_2' => [
            'tag' => 'SCAFFOLDING SAFETY NET',
            'title' => 'Immediate support when variables happen',
            'desc' => 'Ran out of items or changed your mind? Intent + sentence frames + key vocabulary [🔊] rescue you instantly.',
        ],
        'card_3' => [
            'tag' => '10-15S THINKING & RETRY',
            'title' => 'Speak comfortably & Instant retry button',
            'desc' => 'No pressure countdowns. Stumbled on a word? Press [↺ Retry] to clear and re-record immediately.',
        ],

        'simulator' => [
            'calling' => 'Calling 1-on-1: Emma &bull; Barista Melbourne',
            'step_1' => 'Step 1',
            'step_2' => 'Step 2',
            'step_3' => 'Step 3',
            'time_level' => '⏱️ 01:24 &bull; Level A2',
            'live_speech' => '● Live Web Speech',
            'state_1_tag' => 'EMMA JUST SAID:',
            'state_1_text' => '&ldquo;Hi there! Welcome to Melbourne Roastery. What can I get started for you today?&rdquo;',
            'state_2_incident' => '<strong>Real-world twist:</strong> Out of oat milk, Emma asks if you would like soy milk instead?',
            'state_2_scaffold_tag' => 'SCAFFOLDING SAFETY NET:',
            'state_2_tips_badge' => '[💡 Tips enabled]',
            'state_2_sentence' => '&ldquo;Sure, <span class="text-amber-300 underline font-mono bg-amber-400/10 px-1 rounded">soy milk is fine</span>, thanks!&rdquo;',
            'state_2_keywords_label' => 'Keywords:',
            'keyword_soy_milk' => 'soy milk [🔊]',
            'keyword_instead' => 'instead [🔊]',
            'state_3_tag' => 'YOUR TURN TO SPEAK:',
            'state_3_status' => 'Microphone is listening... Take your time and speak!',
            'retry_btn' => 'Retry',
            'done_btn' => 'Done',
            'footer_private' => '100% Private Practice Room',
            'footer_speed' => 'Latency &le; 1.2s',
        ],
    ],

    // Chapter 3: CSI Philosophy
    'chapter_3' => [
        'eyebrow' => 'HUMANISTIC EVALUATION PHILOSOPHY',
        'heading' => 'No Red Scores. Only Empathic CSI Metrics.',
        'subtitle' => 'In real-world communication, what matters most is: <strong>Did you achieve your goal and connect with the other person?</strong> Nobody docks points over a minor preposition mistake.',
        'card_tag' => 'SAMPLE SESSION RESULT',
        'card_title' => 'Communication Success Index (CSI Score)',
        'card_feedback' => '&ldquo;You successfully ordered your desired coffee in just 2 minutes, adapting flexibly to switch to soy milk!&rdquo;',
        'pillar_1_tag' => '40% RESOLUTION',
        'pillar_1_title' => 'Goal Resolution',
        'pillar_1_desc' => 'You clearly conveyed your intent and obtained what you needed (coffee, hotel room, directions).',
        'pillar_2_tag' => '35% ADAPTABILITY',
        'pillar_2_title' => 'Adaptive Agility',
        'pillar_2_desc' => 'Ability to stay calm and naturally pivot when unexpected real-life variables arise.',
        'pillar_3_tag' => '25% FLOW',
        'pillar_3_title' => 'Natural Flow',
        'pillar_3_desc' => 'Response speed, confidence, and steadily diminishing awkward pauses each day.',
    ],

    // Chapter 4: Try Speaking Now (Micro-Voice & CTA)
    'chapter_4' => [
        'eyebrow' => 'HANDS-ON INTERACTIVE PREVIEW',
        'heading_line1' => 'Don\'t wait for perfection.',
        'heading_line2' => 'Start speaking right now.',
        'subtitle' => 'Click below to hear Emma\'s greeting or speak a short sentence into your microphone right on this page:',
        'emma_ask' => 'Emma asks you:',
        'listen_emma_btn' => 'Hear Emma speak [🔊]',
        'emma_question' => '&ldquo;Hi there! What\'s your favorite drink in the morning?&rdquo;',
        'user_sentence_tag' => 'WHAT YOU SAID:',
        'user_sentence_default' => 'Click microphone and say: I love a cup of black coffee.',
        'recording_status' => 'Listening...',
        'record_button' => 'Try speaking a sentence',
        'cta_button' => 'ENTER PRACTICE ROOM NOW &mdash; 100% FREE',
        'badge_no_card' => 'No credit card required',
        'badge_no_login' => 'No sign-up required',
        'badge_in_browser' => 'Runs 100% in browser',

        'js' => [
            'prompt_speech' => '🎙️ Speak any English sentence into your mic...',
            'speech_success' => '👏 Wonderful! ":transcript" responded very naturally and confidently!',
            'speech_fallback' => '🎉 Successfully recognized: Clear and confident pronunciation!',
            'speech_error' => '👍 Recognized well! Try entering the real room for deeper conversations.',
            'speech_not_supported' => 'Your browser does not support Web Speech Synthesis.',
        ],
    ],

    // Footer
    'footer' => [
        'tagline' => 'AI Real-World English Communication Simulator',
        'scenarios' => 'Scenario Catalog',
        'notes' => 'Smart Notes',
        'admin_costs' => 'Cost Admin',
        'rights_reserved' => 'All rights reserved.',
        'security_notice' => 'Web Speech API Audio Processing &bull; 100% Learner Data Privacy',
    ],
];
