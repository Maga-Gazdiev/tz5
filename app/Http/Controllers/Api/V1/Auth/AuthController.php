<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\RoleService;

class AuthController extends Controller
{

    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $this->roleService->assignRoleToUser($user, USER::ROLE_USER);

        $token = $user->createToken('auth_token')->plainTextToken;
        $success = ["user" => $user->name, "token" => $token];
        return response()->json(['success' => $success, 'message' => 'Пользователь успешно зарегистрирован']);
    }

    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        if (auth()->attempt($request->validated())) {
            $user = User::where('email', $request->email)->first();

            if ($user->tokens->where('name', 'auth_token')->first()) {
                return response()->json(['message' => 'Пользователь уже авторизован'], 409);
            }
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => [
                    'name' => $user->name,
                    'token' => $token,
                ],
                'message' => 'Пользователь успешно авторизован'
            ]);
        }

        return response()->json(['error' => 'Не удалось аутентифицировать пользователя. Неверный логин или пароль.'], 401);
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        $user = User::find(auth()->id());

        if ($user) {

            $user->tokens()->delete();

            return response()->json(['message' => 'Вы успешно вышли из аккаунта'], 200);
        }

        return response()->json(['error' => 'Пользователь не найден'], 404);
    }

    public function getUser(): \Illuminate\Http\JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function refreshToken(): \Illuminate\Http\JsonResponse
    {
        $user = User::find(auth()->id());

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->respondWithToken($token);
    }

    protected function respondWithToken(string $token): \Illuminate\Http\JsonResponse
    {
        $user = auth('sanctum')->user();

        if ($user) {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => now()->addMinutes(config('sanctum.expiration'))->diffInSeconds(),
            ]);
        }

        return response()->json(['error' => 'Пользователь не найден'], 404);
    }
}