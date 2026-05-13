@extends('layouts.app')

@section('title', 'Редактирование пользователя')

@section('content')
<div class="admin-page">
    <div class="page-header">
        <h1>Редактирование пользователя</h1>
        <p>Измените данные пользователя {{ $user->name }}</p>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="edit-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Имя</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name', $user->name) }}" 
                       class="form-control @error('name') is-invalid @enderror" 
                       required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email', $user->email) }}" 
                       class="form-control @error('email') is-invalid @enderror" 
                       required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="role_id">Роль</label>
                <select name="role_id" 
                        id="role_id" 
                        class="form-control @error('role_id') is-invalid @enderror" 
                        required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Save SVG Icon</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2"/><path d="M17 21v-8H7v8M7 3v5h8"/></g></svg></span>
                    Сохранить изменения
                </button>
                <a href="{{ route('admin.users') }}" class="btn-cancel">
                    <span>↺</span>
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.admin-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
}

.page-header {
    margin-bottom: 30px;
    text-align: center;
}

.page-header h1 {
    font-size: 32px;
    color: #333;
    margin-bottom: 10px;
}

.page-header p {
    color: #666;
    font-size: 16px;
}

.form-container {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #ECF86E;
    box-shadow: 0 0 0 3px rgba(236, 248, 110, 0.1);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.error-message {
    display: block;
    margin-top: 5px;
    color: #dc3545;
    font-size: 13px;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-save {
    flex: 1;
    padding: 12px;
    background: #000;
    color: #ECF86E;
    border: none;
    border-radius: 40px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-save:hover {
    background: #333;
    transform: translateY(-2px);
}

.btn-cancel {
    flex: 0 0 120px;
    padding: 12px;
    background: #f0f0f0;
    color: #666;
    text-decoration: none;
    border-radius: 40px;
    font-size: 16px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-cancel:hover {
    background: #e0e0e0;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .btn-cancel {
        flex: 1;
    }
}
</style>
@endsection