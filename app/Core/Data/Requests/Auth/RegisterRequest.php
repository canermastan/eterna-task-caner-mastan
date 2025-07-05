<?php

namespace App\Core\Data\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[\pL\s\-\'\.]+$/u', // Unicode letters, spaces, hyphens, apostrophes, dots
            ],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[\pL\s\-\'\.]+$/u',
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^(\+90|0)?[0-9]{10}$/', // Turkish phone format
                'unique:users,phone',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                'unique:users,email',
                'lowercase',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.max' => 'First name may not be greater than 50 characters.',
            'first_name.regex' => 'First name contains invalid characters.',
            'last_name.required' => 'Last name is required.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.max' => 'Last name may not be greater than 50 characters.',
            'last_name.regex' => 'Last name contains invalid characters.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Please enter a valid phone number (e.g. 05551234567).',
            'phone.unique' => 'This phone number is already taken.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'email.lowercase' => 'Email address must be lowercase.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'phone' => 'phone',
            'email' => 'email',
            'password' => 'password',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->email ?? '')),
            'first_name' => trim($this->first_name ?? ''),
            'last_name' => trim($this->last_name ?? ''),
            'phone' => $this->normalizePhone($this->phone ?? ''),
        ]);
    }

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

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        unset($validated['password_confirmation']);

        return $validated;
    }

    /**
     * Get the throttle key for rate limiting.
     */
    public function throttleKey(): string
    {
        return strtolower($this->string('email') . '|' . $this->ip());
    }
}
