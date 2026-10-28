<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProcessTurnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'transcript' => ['required', 'string', 'max:2000'],
            'audio_url' => ['nullable', 'string', 'max:1024'],
            'user_audio_url' => ['nullable', 'string', 'max:1024'],
            'thinking_seconds' => ['nullable', 'numeric', 'min:0', 'max:60'],
            'redo_count' => ['nullable', 'integer', 'min:0', 'max:2'],
            'hints_used' => ['nullable', 'boolean'],
        ];
    }
}
