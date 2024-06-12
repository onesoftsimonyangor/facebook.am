<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'images' => ['required', 'array'],
            'images.*.path' => ['required_with:images', 'string', 'max:10000'],
            'images.*.file_type' => ['required_with:images', 'string', 'in:image,video', 'max:10000'],
            'main_image_path' => ['string', 'in:' . implode(',', array_column($this->images, 'path'))],
        ];
    }
}
