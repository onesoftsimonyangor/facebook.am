<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:100'],
            'surname' => ['string', 'max:100'],
            'email' => ['email', 'max:150'],
            'phone' => ['numeric', 'digits_between:8,15'],
            'birth_date' => ['date', 'before_or_equal:' . now()->subYears(16)->format('Y-m-d')],
        ];
    }

    public function messages()
    {
        return [
            'birth_date.before_or_equal' => 'You must be over 16 years old.',
        ];
    }
}
