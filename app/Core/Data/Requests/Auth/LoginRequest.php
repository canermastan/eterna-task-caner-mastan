<?php

namespace App\Core\Data\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
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
            'login' => [
                'required',
                'string',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8', // Fixed: Increased from 1 to 8 for security
            ],
            'remember' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'Email address or phone number is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'login' => 'login credential',
            'password' => 'password',
        ];
    }

    protected function prepareForValidation(): void
    {
        $login = trim($this->login ?? '');

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $login = strtolower($login);
        } else {
            $login = $this->normalizePhone($login);
        }

        $this->merge([
            'login' => $login,
            'remember' => $this->boolean('remember', false),
        ]);
    }

    /**
     * Normalize phone number format.
     */
    private function normalizePhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert to standard format
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            // 05551234567 -> 5551234567
            $phone = substr($phone, 1);
        } elseif (strlen($phone) === 12 && str_starts_with($phone, '90')) {
            // 905551234567 -> 5551234567
            $phone = substr($phone, 2);
        }

        return $phone;
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $login = $this->input('login');
        $credentials = ['password' => $this->input('password')];

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $login;
        } else {
            $credentials['phone'] = $login;
        }

        $credentials['is_active'] = true;

        if (! Auth::guard('web')->attempt($credentials, $this->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => 'Invalid credentials or account is not active.',
            ]);
        }
    }

    public function throttleKey(): string
    {
        return strtolower($this->string('login').'|'.$this->ip());
    }
}
