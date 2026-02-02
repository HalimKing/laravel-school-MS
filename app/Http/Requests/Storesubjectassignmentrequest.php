<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Storesubjectassignmentrequest extends FormRequest
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
            'teacher_id' => 'required|exists:teachers,id',
            'grade_levels' => 'required|array|min:1',
            'grade_levels.*' => 'required|exists:class_models,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'required|exists:subjects,id',
        ];
    }
}
