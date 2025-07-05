<?php

namespace App\Http\Controllers\Api;

use App\Core\Data\Requests\Auth\LoginRequest;
use App\Core\Data\Requests\Auth\RegisterRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $userData = $request->validated();
                $user = User::create($userData);

                $user->load('role');

                Auth::guard('web')->login($user, remember: true);

                $request->session()->migrate(destroy: true);

                Log::info('User registered and authenticated', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'session_id' => $request->session()->getId(),
                ]);

                return $this->successResponse([
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'full_name' => $user->full_name,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'role' => $user->role->name,
                        'is_active' => $user->is_active,
                        'created_at' => $user->created_at,
                    ],
                    'authenticated' => Auth::guard('web')->check(),
                ], 'Account created and logged in successfully');
            });

        } catch (\Exception $e) {
            Log::error('User registration failed', [
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
            ]);

            return $this->errorResponse('Registration failed. Please try again.', 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $throttleKey = $request->throttleKey();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return $this->errorResponse(
                "Too many login attempts. Please try again in {$seconds} seconds.",
                429
            );
        }

        try {
            $request->authenticate();

            $user = User::with('role')->find(Auth::id());

            $request->session()->regenerate();

            RateLimiter::clear($throttleKey);

            Log::info('User authentication successful', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => $user->role->name,
                    'is_active' => $user->is_active,
                    'email_verified_at' => $user->email_verified_at,
                    'phone_verified_at' => $user->phone_verified_at,
                ],
            ], 'Login successful');

        } catch (ValidationException $e) {
            RateLimiter::hit($throttleKey, 60); // Increment rate limit
            throw $e;
        } catch (\Exception $e) {
            Log::error('Login failed', [
                'login' => $request->input('login'),
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return $this->errorResponse('An error occurred during login', 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('User logout successful', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse(null, 'Logout successful');

        } catch (\Exception $e) {
            Log::error('Logout failed', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return $this->errorResponse('An error occurred during logout', 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        $user = User::with('role')->find(Auth::id());

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'role' => $user->role->name,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'phone_verified_at' => $user->phone_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ], 'User information retrieved');
    }
}
