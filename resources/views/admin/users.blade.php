@extends('layouts.app')

@section('title', 'Управление пользователями')

@section('content')
<div class="admin-users-page">
    <div class="page-header">
        <h1 class="page-title">Управление пользователями</h1>
        <p class="page-subtitle">Всего пользователей: {{ $users->total() }}</p>
    </div>

    <div class="users-table-container">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Дата регистрации</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="user-id">#{{ $user->id }}</td>
                    <td class="user-name">
                        <div class="user-avatar">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                            @else
                                <span class="avatar-placeholder">{{ substr($user->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <span>{{ $user->name }}</span>
                    </td>
                    <td class="user-email">{{ $user->email }}</td>
                    <td class="user-role">
                        <span class="role-badge role-{{ $user->role->name ?? 'user' }}">
                            {{ $user->role->display_name ?? 'Пользователь' }}
                        </span>
                    </td>
                    <td class="user-date">{{ $user->created_at->format('d.m.Y') }}</td>
                    <td class="user-actions">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-edit" title="Редактировать">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 3l4 4-7 7H10v-4l7-7z"/>
                                <path d="M3 21h18"/>
                                <path d="M12 7l5 5"/>
                            </svg>
                            Редактировать
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $users->links() }}
    </div>
</div>

<style>
.admin-users-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.page-header {
    margin-bottom: 30px;
    text-align: center;
}

.page-title {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    position: relative;
    padding-bottom: 10px;
    margin-bottom: 10px;
}

.page-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: #ECF86E;
    border-radius: 2px;
}

.page-subtitle {
    color: #666;
    font-size: 14px;
}

/* Таблица */
.users-table-container {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    border: 1px solid #f0f0f0;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
}

.users-table thead {
    background: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
}

.users-table th {
    padding: 16px 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.users-table td {
    padding: 16px 15px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.users-table tr:hover {
    background: #fafafa;
}

/* Аватар */
.user-name {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #ECF86E;
    color: #000;
    font-size: 16px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Бейдж роли */
.role-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.role-admin {
    background: #000000ff;
    color: white;
}

.role-organizer {
    background: #ECF86E;
    color: #000;
}

.role-user {
    background: #ffffff;
    color: #000000;
}

/* Кнопка редактирования */
.btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: #f0f0f0;
    color: #333;
    text-decoration: none;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-edit:hover {
    background: #ECF86E;
    color: #000;
    transform: translateY(-2px);
}

.user-id {
    font-weight: 600;
    color: #666;
    width: 60px;
}

.user-email {
    color: #666;
}

/* Пагинация */
.pagination-wrapper {
    margin-top: 30px;
    text-align: center;
}

.pagination {
    display: inline-flex;
    gap: 5px;
    list-style: none;
    padding: 0;
}

.pagination li {
    display: inline-block;
}

.pagination a,
.pagination span {
    display: block;
    padding: 8px 14px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    color: #666;
    text-decoration: none;
    transition: all 0.2s ease;
}

.pagination a:hover {
    background: #000;
    color: #ECF86E;
    border-color: #000;
}

.pagination .active span {
    background: #000;
    color: #ECF86E;
    border-color: #000;
}

/* Адаптивность */
@media (max-width: 768px) {
    .admin-users-page {
        padding: 20px 15px;
    }
    
    .page-title {
        font-size: 24px;
    }
    
    .users-table th,
    .users-table td {
        padding: 12px 10px;
    }
    
    .btn-edit span {
        display: none;
    }
    
    .btn-edit {
        padding: 8px;
    }
    
    .user-name span:last-child {
        display: none;
    }
}
</style>
@endsection