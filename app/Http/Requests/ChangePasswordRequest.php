<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => ['required'],
            'new_password' => ['required', 'min:4', 'different:current_password'],
            'new_password_confirmation' => ['required', 'same:new_password'],
        ];
    }
}
