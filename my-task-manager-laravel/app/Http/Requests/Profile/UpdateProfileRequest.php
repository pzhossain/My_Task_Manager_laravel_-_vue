<?php

namespace App\Http\Requests\Profile;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool 
    {   
        // return auth()->check();
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
            'name' => 'sometimes|required|string|max:150',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2MB max
        ];
    }
}
