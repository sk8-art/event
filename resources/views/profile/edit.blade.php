@extends('layouts.app')

@section('title', 'Редактирование профиля')

@section('content')
<div class="profile-edit-page">
    <div class="page-header">
        <h1>Редактирование профиля</h1>
        <p class="header-description">Измените фото и имя пользователя</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- ОСНОВНАЯ ФОРМА -->
    <div class="profile-edit-container">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-edit-form">
            @csrf

            <!-- Блок с фото -->
            <div class="avatar-section">
                <h2>Фото профиля</h2>
                
                <div class="avatar-container">
                    <!-- Аватар с УНИКАЛЬНЫМИ ID для блока редактирования -->
                    <div class="current-avatar" id="editAvatarContainer">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="avatar-image" id="editAvatarImage">
                        @else
                            <div class="avatar-placeholder" id="editAvatarPlaceholder">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Кнопки в ряд -->
                    <div class="avatar-actions">
                        <label for="avatar" class="upload-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Camera SVG Icon</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></g></svg>
                            Выбрать фото
                        </label>
                        <input type="file" 
                               name="avatar" 
                               id="avatar" 
                               accept="image/*" 
                               class="upload-input"
                               onchange="previewAvatar(this)">
                        
                        @if(auth()->user()->avatar)
                        <button type="button" class="delete-avatar" onclick="confirmDelete()">
                            <span class="delete-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Baseline-delete-outline SVG Icon</title><path fill="currentColor" d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM8 9h8v10H8zm7.5-5l-1-1h-5l-1 1H5v2h14V4z"/></svg></span>
                            Удалить фото
                        </button>
                        @endif
                    </div>
                    
                    <!-- Подсказка с названием файла -->
                    <p class="avatar-hint" id="editFileHint">Выберите новое фото для предпросмотра</p>
                </div>
            </div>

            <!-- Блок с именем и email -->
            <div class="info-section">
                <h2>Основная информация</h2>

                <div class="form-group">
                    <label for="name">Имя и фамилия</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', auth()->user()->name) }}" 
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
                           value="{{ old('email', auth()->user()->email) }}" 
                           class="form-control @error('email') is-invalid @enderror" 
                           required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Кнопки -->
            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Save SVG Icon</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2"/><path d="M17 21v-8H7v8M7 3v5h8"/></g></svg>
                    Сохранить изменения
                </button>
                <a href="{{ route('profile.orders') }}" class="btn-cancel">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Скрытая форма для удаления аватара -->
@if(auth()->user()->avatar)
<form id="deleteAvatarForm" action="{{ route('profile.avatar.delete') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endif

<!-- JavaScript для предпросмотра ТОЛЬКО в блоке редактирования -->
<script>
function previewAvatar(input) {
    // Используем ID только из блока редактирования
    const fileHint = document.getElementById('editFileHint');
    const avatarImage = document.getElementById('editAvatarImage');
    const avatarPlaceholder = document.getElementById('editAvatarPlaceholder');
    const avatarContainer = document.getElementById('editAvatarContainer');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Проверяем размер файла (2MB)
        if (file.size > 2 * 1024 * 1024) {
            fileHint.innerHTML = '❌ Файл слишком большой (максимум 2MB)';
            fileHint.style.color = '#ef4444';
            input.value = '';
            return;
        }
        
        // Проверяем тип файла
        if (!file.type.match('image.*')) {
            fileHint.innerHTML = '❌ Можно загружать только изображения';
            fileHint.style.color = '#ef4444';
            input.value = '';
            return;
        }
        
        fileHint.innerHTML = ` Выбрано: ${file.name}`;
        fileHint.style.color = '#28a745';
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Если есть изображение - обновляем его
            if (avatarImage) {
                avatarImage.src = e.target.result;
            } 
            // Если был плейсхолдер - заменяем на изображение
            else if (avatarPlaceholder) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Avatar';
                img.className = 'avatar-image';
                img.id = 'editAvatarImage';
                
                avatarContainer.innerHTML = '';
                avatarContainer.appendChild(img);
            }
        }
        
        reader.readAsDataURL(file);
    } else {
        fileHint.innerHTML = 'Выберите новое фото для предпросмотра';
        fileHint.style.color = '#6c757d';
    }
}

function confirmDelete() {
    if (confirm('Удалить фото профиля?')) {
        document.getElementById('deleteAvatarForm').submit();
    }
}
</script>

<style>
/* Все стили как в предыдущем рабочем варианте */
.profile-edit-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 30px 20px;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 28px;
    color: #333;
    margin-bottom: 5px;
}

.header-description {
    color: #666;
    font-size: 16px;
}

.profile-edit-container {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Секция аватара */
.avatar-section {
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 1px solid #f0f0f0;
}

.avatar-section h2 {
    font-size: 18px;
    margin-bottom: 20px;
    color: #333;
}

.avatar-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.current-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #ECF86E;
    color: #000;
    font-size: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Кнопки в ряд */
.avatar-actions {
    display: flex;
    gap: 15px;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}

.upload-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #000000ff;
    color: white;
    border-radius: 30px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
    white-space: nowrap;
}

.upload-label:hover {
    transform: translateY(-2px);
}

.upload-input {
    display: none;
}

.delete-avatar {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #fee2e2;
    color: #ef4444;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
    white-space: nowrap;
}

.delete-avatar:hover {
    background: #fecaca;
    transform: translateY(-2px);
}

.avatar-hint {
    color: #6c757d;
    font-size: 14px;
    margin: 0;
    font-style: italic;
    transition: color 0.3s;
}

/* Форма */
.info-section h2 {
    font-size: 18px;
    margin-bottom: 20px;
    color: #333;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #555;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #bbbbbbff;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #000;
    box-shadow: 0 0 0 3px rgba(111, 111, 111, 0.1);
}

.form-control.is-invalid {
    border-color: #ef4444;
}

.error-message {
    display: block;
    margin-top: 5px;
    color: #ef4444;
    font-size: 13px;
}

/* Кнопки действий */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-save {
    flex: 1;
    padding: 14px;
    background: #000;
    color: white;
    border: none;
    border-radius: 10px;
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
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(92, 92, 92, 0.3);
}

.btn-cancel {
    flex: 0 0 120px;
    padding: 14px;
    background: #f0f0f0;
    color: #666;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s;
}

.btn-cancel:hover {
    background: #e0e0e0;
}

/* Адаптивность */
@media (max-width: 768px) {
    .avatar-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .upload-label,
    .delete-avatar {
        width: 100%;
        justify-content: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-cancel {
        flex: 1;
    }
}
</style>
@endsection