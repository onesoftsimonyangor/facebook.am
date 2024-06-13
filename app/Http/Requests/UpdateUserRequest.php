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
            'images' => ['array'],
            'images.*.path' => ['required_with:images', 'string', "max:10000"],
            'images.*.file_type' => ['required_with:images', 'in:image,video', 'max:10000'],
            'name' => ['string', 'max:100'],
            'surname' => ['string', 'max:100'],
            'email' => ['email', 'max:150'],
            'phone' => ['numeric', 'digits_between:8,15'],
            'birth_date' => ['date', 'before_or_equal:' . now()->subYears(16)->format('Y-m-d')],
            'password' => ['min:4'],
            'confirm_password' => ['same:password', 'min:4'],
        ];
    }
}
