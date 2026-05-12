@extends('layouts.app')

@section('title', 'Создание мероприятия')

@section('content')
<div class="organizer-page">
    <div class="page-header">
        <h1>Создание мероприятия</h1>
    </div>

    <div class="form-container">
        <form action="{{ route('organizer.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Основная информация -->
            <div class="form-section">
                <h2>Основная информация</h2>
                
                <div class="form-group">
                    <label for="title">Название мероприятия <span class="required">*</span></label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}" 
                           class="form-control @error('title') is-invalid @enderror" 
                           placeholder="Например: Концерт группы 'Звери'"
                           required>
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Описание <span class="required">*</span></label>
                    <textarea name="description" 
                              id="description" 
                              rows="5" 
                              class="form-control @error('description') is-invalid @enderror" 
                              placeholder="Расскажите подробнее о мероприятии..."
                              required>{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="type">Тип мероприятия <span class="required">*</span></label>
                        <select name="type" 
                                id="type" 
                                class="form-control @error('type') is-invalid @enderror" 
                                required>
                            <option value="">Выберите тип</option>
                            <option value="concert" {{ old('type') == 'concert' ? 'selected' : '' }}>🎵 Концерт</option>
                            <option value="festival" {{ old('type') == 'festival' ? 'selected' : '' }}>🎪 Фестиваль</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>🎭 Другое</option>
                        </select>
                        @error('type')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Дата и место -->
            <div class="form-section">
                <h2>Дата и место</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Дата и время <span class="required">*</span></label>
                        <input type="datetime-local" 
                               name="date" 
                               id="date" 
                               value="{{ old('date') }}" 
                               class="form-control @error('date') is-invalid @enderror" 
                               required>
                        @error('date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="location">Город <span class="required">*</span></label>
                        <input type="text" 
                               name="location" 
                               id="location" 
                               value="{{ old('location') }}" 
                               class="form-control @error('location') is-invalid @enderror" 
                               placeholder="Москва"
                               required>
                        @error('location')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Точный адрес <span class="required">*</span></label>
                    <input type="text" 
                           name="address" 
                           id="address" 
                           value="{{ old('address') }}" 
                           class="form-control @error('address') is-invalid @enderror" 
                           placeholder="ул. Тверская, д. 7"
                           required>
                    @error('address')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Типы билетов -->
            <div class="form-section">
                <div class="section-header">
                    <h2>Типы билетов</h2>
                    <span class="section-note">Оставьте 0, если тип не нужен</span>
                </div>
                
                <div class="tickets-grid">
                    <!-- Стандартный билет -->
                    <div class="ticket-card">
                        <div class="ticket-card-header">
                            <span class="ticket-icon"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"><title>Ticket SVG Icon</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2z"/></svg></span>
                            <span class="ticket-name">Стандартный</span>
                        </div>
                                                
                        <div class="ticket-fields">
                            <div class="field-group">
                                <label class="field-label">Количество</label>
                                <input type="number" 
                                       name="standard_tickets" 
                                       value="{{ old('standard_tickets', 0) }}" 
                                       min="0" 
                                       class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Цена (₽)</label>
                                <input type="number" 
                                       name="standard_price" 
                                       value="{{ old('standard_price', 0) }}" 
                                       min="0" 
                                       class="field-input">
                            </div>
                        </div>
                    </div>

                    <!-- Fan билет -->
                    <div class="ticket-card">
                        <div class="ticket-card-header">
                            <span class="ticket-icon"><svg width="26" viewBox="0 0 24 24" fill="#f86ee6ff" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M11.245 4.174C11.4765 3.50808 11.5922 3.17513 11.7634 3.08285C11.9115 3.00298 12.0898 3.00298 12.238 3.08285C12.4091 3.17513 12.5248 3.50808 12.7563 4.174L14.2866 8.57639C14.3525 8.76592 14.3854 8.86068 14.4448 8.93125C14.4972 8.99359 14.5641 9.04218 14.6396 9.07278C14.725 9.10743 14.8253 9.10947 15.0259 9.11356L19.6857 9.20852C20.3906 9.22288 20.743 9.23007 20.8837 9.36432C21.0054 9.48051 21.0605 9.65014 21.0303 9.81569C20.9955 10.007 20.7146 10.2199 20.1528 10.6459L16.4387 13.4616C16.2788 13.5829 16.1989 13.6435 16.1501 13.7217C16.107 13.7909 16.0815 13.8695 16.0757 13.9507C16.0692 14.0427 16.0982 14.1387 16.1563 14.3308L17.506 18.7919C17.7101 19.4667 17.8122 19.8041 17.728 19.9793C17.6551 20.131 17.5108 20.2358 17.344 20.2583C17.1513 20.2842 16.862 20.0829 16.2833 19.6802L12.4576 17.0181C12.2929 16.9035 12.2106 16.8462 12.1211 16.8239C12.042 16.8043 11.9593 16.8043 11.8803 16.8239C11.7908 16.8462 11.7084 16.9035 11.5437 17.0181L7.71805 19.6802C7.13937 20.0829 6.85003 20.2842 6.65733 20.2583C6.49056 20.2358 6.34626 20.131 6.27337 19.9793C6.18915 19.8041 6.29123 19.4667 6.49538 18.7919L7.84503 14.3308C7.90313 14.1387 7.93218 14.0427 7.92564 13.9507C7.91986 13.8695 7.89432 13.7909 7.85123 13.7217C7.80246 13.6435 7.72251 13.5829 7.56262 13.4616L3.84858 10.6459C3.28678 10.2199 3.00588 10.007 2.97101 9.81569C2.94082 9.65014 2.99594 9.48051 3.11767 9.36432C3.25831 9.23007 3.61074 9.22289 4.31559 9.20852L8.9754 9.11356C9.176 9.10947 9.27631 9.10743 9.36177 9.07278C9.43726 9.04218 9.50414 8.99359 9.55657 8.93125C9.61593 8.86068 9.64887 8.76592 9.71475 8.57639L11.245 4.174Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg></span>
                            <span class="ticket-name">Фан-зона</span>
                        </div>
                                                
                        <div class="ticket-fields">
                            <div class="field-group">
                                <label class="field-label">Количество</label>
                                <input type="number" 
                                       name="fan_tickets" 
                                       value="{{ old('fan_tickets', 0) }}" 
                                       min="0" 
                                       class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Цена (₽)</label>
                                <input type="number" 
                                       name="fan_price" 
                                       value="{{ old('fan_price', 0) }}" 
                                       min="0" 
                                       class="field-input">
                            </div>
                        </div>
                    </div>

                    <!-- VIP билет -->
                    <div class="ticket-card">
                        <div class="ticket-card-header">
                            <span class="ticket-icon"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 14 14"><title>Interface-award-crown-reward-social-rating-media-queen-vip-king-crown SVG Icon</title><path fill="#ECF86E" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="m13.5 4l-3 3L7 2L3.5 7l-3-3v6.5A1.5 1.5 0 0 0 2 12h10a1.5 1.5 0 0 0 1.5-1.5Z"/></svg></span>
                            <span class="ticket-name">VIP</span>
                        </div>
                                           
                        <div class="ticket-fields">
                            <div class="field-group">
                                <label class="field-label">Количество</label>
                                <input type="number" 
                                       name="vip_tickets" 
                                       value="{{ old('vip_tickets', 0) }}" 
                                       min="0" 
                                       class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Цена (₽)</label>
                                <input type="number" 
                                       name="vip_price" 
                                       value="{{ old('vip_price', 0) }}" 
                                       min="0" 
                                       class="field-input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Изображение -->
            <div class="form-section">
                <h2>Изображение</h2>
                
                <div class="image-upload-area">
                    <div class="image-upload-preview" id="imagePreview">
                        <span class="preview-placeholder"><svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24"><title>Camera SVG Icon</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></g></svg></span>
                        <img src="#" alt="Preview" style="display: none;">
                    </div>
                    
                    <div class="upload-controls">
                        <label for="image" class="upload-btn">
                            Выбрать изображение
                        </label>
                        <input type="file" 
                               name="image" 
                               id="image" 
                               accept="image/*" 
                               class="hidden-input @error('image') is-invalid @enderror" 
                               required
                               onchange="previewImage(this)">
                        <p class="upload-hint">JPEG, PNG, JPG. Макс. 2MB</p>
                        @error('image')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    Создать мероприятие
                </button>
                <a href="{{ route('organizer.events') }}" class="btn-secondary">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');
    const placeholder = preview.querySelector('.preview-placeholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            img.src = e.target.result;
            img.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
.organizer-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
}

.page-header {
    margin-bottom: 40px;
    text-align: center;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin: 0 0 10px;
}

.header-description {
    color: #666;
    font-size: 16px;
    margin: 0;
}

.form-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.form-section {
    padding: 30px;
    border-bottom: 1px solid #e9ecef;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h2 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0 0 20px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h2 {
    margin: 0;
}

.section-note {
    color: #999;
    font-size: 13px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
}

.required {
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s;
    background: white;
}

.form-control:hover {
    border-color: #adb5bd;
}

.form-control:focus {
    outline: none;
    border-color: #000;
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%2F%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    padding-right: 40px;
}

/* Tickets Grid */
.tickets-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.ticket-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.2s;
}

.ticket-card:hover {
    border-color: #000;
}

.ticket-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.ticket-icon {
    font-size: 24px;
}

.ticket-name {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.ticket-fields {
    display: flex;
    gap: 10px;
}

.field-group {
    flex: 1;
}

.field-label {
    display: block;
    font-size: 12px;
    color: #999;
    margin-bottom: 4px;
}

.field-input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s;
}

.field-input:focus {
    outline: none;
    border-color: #000;
}

/* Image Upload */
.image-upload-area {
    display: flex;
    align-items: center;
    gap: 30px;
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
}

.image-upload-preview {
    width: 150px;
    height: 150px;
    border-radius: 8px;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.preview-placeholder {
    font-size: 48px;
    color: #adb5bd;
}

.image-upload-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-btn {
    display: inline-block;
    padding: 10px 20px;
    background: #000;
    color: #ECF86E;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.upload-btn:hover {
    background: #333;
    transform: translateY(-2px);
}

.hidden-input {
    display: none;
}

.upload-hint {
    font-size: 12px;
    color: #999;
    margin-top: 10px;
}

/* Form Actions */
.form-actions {
    padding: 30px;
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    background: white;
}

.btn-primary, .btn-secondary {
    padding: 12px 30px;
    border: none;
    border-radius: 30px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: #ECF86E;
    color: #000;
}

.btn-primary:hover {
    transform: translateY(-2px);
}

.btn-secondary {
    background: transparent;
    border: 1px solid #dee2e6;
    color: #666;
}

.btn-secondary:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
}

.error-message {
    display: block;
    margin-top: 5px;
    color: #dc3545;
    font-size: 13px;
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .tickets-grid {
        grid-template-columns: 1fr;
    }
    
    .image-upload-area {
        flex-direction: column;
        text-align: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-primary, .btn-secondary {
        width: 100%;
        text-align: center;
    }
}
</style>
@endsection