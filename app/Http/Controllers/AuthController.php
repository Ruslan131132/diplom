<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            Log::create([
                'text' => 'Неудачная попытка авторизации: <span>' . $request->email . '</span>',
                'type' => Log::FAIL,
                'user_agent' => $request->userAgent() ?? null,
                'ip' => $request->ip() ?? null,
            ]);
            return self::error('Неправильный email/пароль', 404);
        }
        $user = Auth::user();
        Log::create([
            'text' => 'Успешная авторизация в системе',
            'type' => Log::FAIL,
            'user_agent' => $request->userAgent() ?? null,
            'ip' => $request->ip() ?? null,
            'user_id' => $user->id
        ]);
        $token = $user->createToken('API_TOKEN')->plainTextToken;

        return self::response($token, 'Успешная авторизация');
    }

    public function launchApp(Request $request)
    {
        $user = auth('sanctum')->user() ?? false;
        Log::create([
            'text' => $user ? 'Успешная авторизация в системе по токену' : 'Попытка входа по токену ' . ($request->bearerToken() ?? ''),
            'type' => $user ? Log::SUCCESS : Log::FAIL,
            'user_agent' => $request->userAgent() ?? null,
            'ip' => $request->ip() ?? null,
            'user_id' => $user->id ?? null
        ]);

        return self::response(['success' => !empty($user)], $user ? 'Токен валидный' : 'Токен не валиден', $user ? 200 : 401);
    }
}

