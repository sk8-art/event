@extends('layouts.app')

@section('title', 'Панель администратора')

@section('content')
<div class="admin-page">
    <h1>Панель администратора</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_users'] }}</div>
            <div class="stat-label">Пользователей</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_events'] }}</div>
            <div class="stat-label">Мероприятий</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_orders'] }}</div>
            <div class="stat-label">Заказов</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} ₽</div>
            <div class="stat-label">Выручка</div>
        </div>
    </div>
    
    <div class="admin-links">
        <a href="{{ route('admin.users') }}" class="admin-link">Управление пользователями</a>
        <a href="{{ route('admin.events') }}" class="admin-link">Управление мероприятиями</a>
        <a href="{{ route('admin.roles') }}" class="admin-link">Управление ролями</a>
    </div>
</div>
@endsection