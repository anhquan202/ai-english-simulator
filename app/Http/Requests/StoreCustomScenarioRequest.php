<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomScenarioRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'location_name' => ['nullable', 'string', 'max:150'],
            'detailed_description' => ['required', 'string'],
            'user_role' => ['required', 'string', 'max:100'],
            'ai_name' => ['required', 'string', 'max:100'],
            'ai_role' => ['required', 'string', 'max:100'],
            'twist_prompt' => ['nullable', 'string'],
            'goals' => ['nullable', 'string'],
            'first_message' => ['nullable', 'string', 'max:500'],
            'level' => ['required', 'string', 'in:A1,A2,B1,B2,a1,a2,b1,b2'],
            'ai_tone' => ['nullable', 'string', 'in:friendly,professional,challenging'],
            'action' => ['nullable', 'string', 'in:save,start_practice'],
        ];
    }
}
