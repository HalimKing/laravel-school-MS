<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssessmentRequest extends FormRequest
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
            'class_level_ids' => 'required|array|min:1',
            'class_level_ids.*' => 'exists:class_models,id',
            'components' => 'required|array|min:1',
            'components.*.name' => 'required|string|max:255',
            'components.*.percentage' => 'required|numeric|min:0.01|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'components.*.percentage.max' => 'Each component weight must not exceed 100%',
            'components.required' => 'At least one assessment component is required',
        ];
    }

    /**
     * Get the total weight of all components.
     */
    public function getTotalWeight(): float
    {
        return collect($this->components)->sum('percentage');
    }
}
