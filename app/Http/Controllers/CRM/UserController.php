<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with('todayLogs', 'roles')
            ->when($request->email ?? false, function ($query) use ($request) {
                $query->where('email', 'like', '%' . $request->email . '%');
            })

            ->when($request->role ?? false, function ($query) use ($request) {
                $query->whereHas('roles', function ($subQuery) use ($request) {
                    $subQuery->where('name', $request->role);
                });
            })
            ->when(auth()->user()->id ?? false, function ($query) use ($request) {
                $query->where('id', '<>', auth()->user()->id);
            })
            ->get()
            ->map(function ($item) {
                return $item->append('last_activation');
            })
            ->sortBy([['last_activation', 'desc']]);

        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function create(Request $request)
    {
        $roles = Role::all();
        if ($request->isMethod('get')) {
            return view('users.add', compact('roles'));
        }
        $credentials = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['required'],
            'role' => ['required', 'exists:roles,id'],
            'password' => ['min:10', 'required_with:password_confirmation', 'same:password_confirmation'],
            'password_confirmation' => ['min:10']
        ], [
            'email' => 'Поле имеет неправильный формат',
            'required' => 'Это поле обязательно',
            'unique' => 'Пользователь с такой почтой уже существует',
            'same' => 'Пароли должны совпадать',
            'required_with' => 'Заполните оба поля',
            'min' => 'Минимальная длина пароля - 10 символов'
        ]);

        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
        ]);
        $user->assignRole([(int)$credentials['role']]);

        return redirect()->route('users.show', ['id' => $user->id])->with('success', 'Пользователь успешно добавлен!');
    }

    public function show($id)
    {
        $user = User::with('roles')->find($id);
        $logs = $user?->logs()->orderByDesc('created_at')->get() ?? collect([]);

        return view('users.show', compact('user', 'logs'));
    }

    public function toggleBanUser($id, Request $request)
    {
        $user = User::where('id', $id)->update(['active' => $request->routeIs('user.unban')]);

        return redirect()->back()->with(['success' => 'Данные успешно изменены!']);
    }


}
