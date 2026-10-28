<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePracticeSessionRequest extends FormRequest
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
            'scenario_id' => ['required', 'integer', 'exists:scenarios,id'],
            'character_id' => ['required', 'integer', 'exists:characters,id'],
            'level' => ['required', 'string', 'in:A1,A2,B1,B2,a1,a2,b1,b2'],
            'practice_language' => ['nullable', 'string', 'in:en,ja,zh,ko,es,EN,JA,ZH,KO,ES'],
            'guest_token' => ['nullable', 'string', 'max:100'],
        ];
    }
}
