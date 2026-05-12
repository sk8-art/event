@extends('layouts.app')

@section('title', 'Управление пользователями')

@section('content')
<div class="admin-page">
    <h1>Пользователи</h1>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Email</th>
                <th>Роль</th>
                <th>Дата регистрации</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role->display_name ?? 'Нет' }}</td>
                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                <td>
                    <a href="{{ route('admin.users.edit', $user) }}">Редактировать</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    {{ $users->links() }}
</div>
@endsection