<?php

namespace App\Http\Requests\User\Manage;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|string|min:8|alpha_num|unique:users,username',
            'password' => 'required|string|regex:/^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])(?=.*[!@#$&*()]).{8,})\S$/',
            'name' => 'nullable|string|min:3|regex:/^[a-zA-Z_\s]+$/i',
            'status' => 'nullable|string|',
            'idSiswa' => 'nullable|string|numeric'
        ];
    }
}
