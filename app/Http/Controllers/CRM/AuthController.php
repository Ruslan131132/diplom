<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('login');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'required' => 'Это поле обязательно'
        ]);

        if (!Auth::attempt($credentials)) {
            Log::create([
                'text' => 'Неудачная попытка авторизации в CRM: <span>' . $request->email . '</span>',
                'type' => Log::FAIL,
                'user_agent' => $request->userAgent() ?? null,
                'ip' => $request->ip() ?? null,
            ]);

            return redirect()->back()->withErrors(['auth' => 'Неправильный логин/пароль']);
        }

        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            Log::create([
                'text' => 'Неудачная попытка авторизации в CRM под пользователем не имеющих прав: <span>' . $credentials['email'] . '</span>',
                'type' => Log::WARNING,
                'user_agent' => $request->userAgent() ?? null,
                'ip' => $request->ip() ?? null,
            ]);

            return redirect()->back()->withErrors(['auth' => 'В доступе отказано']);
        }

        return redirect()->route('main')->with(['success' => 'Добро пожаловать, ' . $user->name]);
    }
}

