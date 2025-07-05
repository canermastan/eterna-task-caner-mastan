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
        $throttleKey = $request->throttleKey();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return $this->errorResponse(
                "Too many registration attempts. Please try again in {$seconds} seconds.",
                429
            );
        }

        try {
            $result = DB::transaction(function () use ($request) {
                $userData = $request->validated();
                $user = User::create($userData);

                $user->load('role');

                $token = $user->createToken('auth_token')->plainTextToken;

                Log::info('User registered and authenticated', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return [
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
                    'token' => $token,
                ];
            });

            RateLimiter::clear($throttleKey);

            return $this->successResponse($result, 'Account created and logged in successfully');

        } catch (\Exception $e) {
            RateLimiter::hit($throttleKey, 300); // 5 minutes lockout

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
            $validatedData = $request->validated();
            $login = $validatedData['login'];
            
            $credentials = ['password' => $validatedData['password']];
            
            if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $credentials['email'] = $login;
            } else {
                $credentials['phone'] = $login;
            }
            
            $credentials['is_active'] = true;
            
            if (!Auth::attempt($credentials)) {
                RateLimiter::hit($throttleKey, 60);
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user = User::with('role')->find(Auth::id());

            $token = $user->createToken('auth_token')->plainTextToken;

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
                'token' => $token,
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

            $request->user()->currentAccessToken()->delete();

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
        $user = $request->user()->load('role');

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
