@extends('layouts.crm')

@section('styles')
    <style>
        .log-text span {
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center">
        <form class="form-inline my-4 d-flex align-items-center" method="GET" action="{{route('users')}}">
            <div class="form-group mx-sm-3 mb-2">
                <label for="created_to" class="sr-only">Роли</label>
                <select class="form-select" aria-label="Default select example" name="role">
                    <option @if(!request()->role || request()->role == 0) selected @endif value="0">Все</option>
                    @foreach($roles as $role)
                        <option @if(request()->role == $role->name) selected @endif value="{{$role->name}}">{{$role->ru ?? $role->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <label for="created_to" class="sr-only">Email</label>
                <input type="text" class="form-control" name="email" value="{{request()->email ?? null}}">
            </div>
            <button type="submit" class="btn btn-primary mt-3" style="max-height: 40px">Поиск</button>
        </form>
        <a href="{{route('users.add')}}" class="btn btn-success d-block mt-3" style="max-height: 40px">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"></path>
            </svg>
            Добавить
        </a>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Пользователь</th>
            <th scope="col">Последняя активация QR</th>
            <th scope="col">Активность за сегодня</th>
            <th scope="col">Статус</th>
        </tr>
        </thead>
        <tbody>

        @php
            $isUserHasAccessToEdit = auth()->user()?->hasRole('admin') ?? false;
        @endphp
        @forelse($users as $user)
            <tr>
                <th onclick="location.href = '{{route('users.show', ['id' => $user->id])}}'" scope="row">{{$user->id}}</th>
                <td onclick="location.href = '{{route('users.show', ['id' => $user->id])}}'">{{$user->email}}</td>
                <td class="text-nowrap">{{$user->last_activation ?? '-'}}</td>
                <td class="log-text" >{!! $user->todayLogs->count() !!}</td>
                <td>
                    @if(!$isUserHasAccessToEdit || in_array('admin', $user->roles->pluck('name')->toArray()))
                        <span class="{{$user->active ? 'text-success' : 'text-danger'}}">{{$user->active ? 'Разблокирован' : 'Заблокирован'}}</span>
                        <br/>
                        <span class="text-secondary" style="font-size: 10px">У вас нет прав на действия</span>
                    @else
                        @if($user->active)
                            <button type="button" class="btn btn-outline-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ban" viewBox="0 0 16 16">
                                    <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"></path>
                                </svg>
                                Заблокировать
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-all" viewBox="0 0 16 16">
                                    <path d="M8.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992zm-.92 5.14.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486z"></path>
                                </svg>
                                Разблокировать
                            </button>
                        @endif
                    @endif
                </td>
                <td>

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="table-active text-center">Данные не найдены</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@endsection

@section('js')

@endsection
