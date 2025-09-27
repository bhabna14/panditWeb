<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $admin = $this->route('admin'); // null on create
        $isUpdate = !is_null($admin);

        $base = [
            'name'   => ['required','string','max:150'],
            'email'  => [
                'required','email','max:190',
                Rule::unique('admins','email')->ignore($admin?->id),
            ],
            'status' => ['required', Rule::in(['active','inactive'])],
            'role'   => ['nullable', Rule::in(['admin','superadmin'])], // will be normalized in controller
        ];

        if ($isUpdate) {
            $base['password'] = ['nullable','string','min:8','confirmed'];
        } else {
            $base['password'] = ['required','string','min:8','confirmed'];
        }

        return $base;
    }

    public function messages(): array
    {
        return [
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
