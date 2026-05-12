@extends('layouts.app')

@section('title', 'Редактирование мероприятия')

@section('content')
<div class="organizer-page">
    <div class="page-header">
        <h1 class="page-title">Редактирование мероприятия</h1>
    </div>

    <div class="form-container">
        <form action="{{ route('organizer.update', $event) }}" method="POST" enctype="multipart/form-data" class="event-form">
            @csrf
            @method('PUT')

            <!-- Основная информация (на всю ширину) -->
            <div class="form-section full-width">
                <h3 class="section-title">Основная информация</h3>
                
                <div class="form-group">
                    <label for="title">Название мероприятия <span class="required">*</span></label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $event->title) }}" 
                           class="form-control @error('title') is-invalid @enderror" 
                           required>
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Описание <span class="required">*</span></label>
                    <textarea name="description" 
                              id="description" 
                              rows="6" 
                              class="form-control @error('description') is-invalid @enderror" 
                              required>{{ old('description', $event->description) }}</textarea>
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
                            <option value="concert" {{ old('type', $event->type) == 'concert' ? 'selected' : '' }}>Концерт</option>
                            <option value="festival" {{ old('type', $event->type) == 'festival' ? 'selected' : '' }}>Фестиваль</option>
                            <option value="other" {{ old('type', $event->type) == 'other' ? 'selected' : '' }}>Другое</option>
                        </select>
                        @error('type')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status">Статус <span class="required">*</span></label>
                        <select name="status" 
                                id="status" 
                                class="form-control @error('status') is-invalid @enderror" 
                                required>
                            <option value="active" {{ old('status', $event->status) == 'active' ? 'selected' : '' }}>Активно</option>
                            <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Отменено</option>
                            <option value="completed" {{ old('status', $event->status) == 'completed' ? 'selected' : '' }}>Завершено</option>
                        </select>
                        @error('status')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Две колонки -->
            <div class="form-columns">
                <!-- Левая колонка - Дата и место -->
                <div class="form-section">
                    <h3 class="section-title">Дата и место проведения</h3>
                    
                    <div class="form-group">
                        <label for="date">Дата и время <span class="required">*</span></label>
                        <input type="datetime-local" 
                               name="date" 
                               id="date" 
                               value="{{ old('date', $event->date->format('Y-m-d\TH:i')) }}" 
                               class="form-control @error('date') is-invalid @enderror" 
                               required>
                        @error('date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="location">Город/Локация <span class="required">*</span></label>
                        <input type="text" 
                               name="location" 
                               id="location" 
                               value="{{ old('location', $event->location) }}" 
                               class="form-control @error('location') is-invalid @enderror" 
                               required>
                        @error('location')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="address">Точный адрес <span class="required">*</span></label>
                        <input type="text" 
                               name="address" 
                               id="address" 
                               value="{{ old('address', $event->address) }}" 
                               class="form-control @error('address') is-invalid @enderror" 
                               required>
                        @error('address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Правая колонка - Билеты и цена -->
                <div class="form-section">
                    <h3 class="section-title">Билеты и цена</h3>
                    
                    <div class="form-group">
                        <label for="price">Цена билета (₽) <span class="required">*</span></label>
                        <input type="number" 
                               name="price" 
                               id="price" 
                               value="{{ old('price', $event->price) }}" 
                               min="0" 
                               step="1" 
                               class="form-control @error('price') is-invalid @enderror" 
                               required>
                        @error('price')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="total_tickets">Количество билетов <span class="required">*</span></label>
                        <input type="number" 
                               name="total_tickets" 
                               id="total_tickets" 
                               value="{{ old('total_tickets', $event->total_tickets) }}" 
                               min="1" 
                               class="form-control @error('total_tickets') is-invalid @enderror" 
                               required>
                        @error('total_tickets')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="tickets-info">
                        <div class="ticket-stat">
                            <span class="stat-label">Всего билетов</span>
                            <span class="stat-value">{{ $event->total_tickets }}</span>
                        </div>
                        <div class="ticket-stat">
                            <span class="stat-label">Продано</span>
                            <span class="stat-value">{{ $event->getSoldTicketsCount() }}</span>
                        </div>
                        <div class="ticket-stat">
                            <span class="stat-label">Доступно</span>
                            <span class="stat-value">{{ $event->available_tickets }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Изображение (на всю ширину) -->
            <div class="form-section full-width">
                <h3 class="section-title">Изображение мероприятия</h3>
                
                <div class="current-image">
                    <span class="current-image-label">Текущее изображение:</span>
                    <div class="image-wrapper">
                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="current-img">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="image">Загрузить новое изображение (необязательно)</label>
                    <div class="image-upload-container">
                        <input type="file" 
                               name="image" 
                               id="image" 
                               accept="image/*" 
                               class="form-control @error('image') is-invalid @enderror" 
                               onchange="previewImage(this)">
                        <div id="image-preview" class="image-preview"></div>
                    </div>
                    <small class="form-text">Оставьте пустым, чтобы сохранить текущее изображение</small>
                    @error('image')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Кнопки -->
            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Сохранить изменения
                </button>
                <a href="{{ route('organizer.events') }}" class="btn-cancel">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = 'Preview';
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection

@push('styles')
<style>
.organizer-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
    display: flex;
    flex-direction: column;
    gap: 40px;
}

.page-header {
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.page-title {
    font-size: 36px;
    font-weight: 700;
    color: #333;
    position: relative;
    padding-bottom: 15px;
}


.page-subtitle {
    color: #666;
    font-size: 18px;
}

/* Форма */
.form-container {
    background: white;
    border-radius: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 40px;
}

.event-form {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

/* Две колонки */
.form-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.form-section {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-section.full-width {
    grid-column: 1 / -1;
}

.section-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    position: relative;
    padding-left: 15px;
}

.section-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 5px;
    bottom: 5px;
    width: 4px;
    background: #ECF86E;
    border-radius: 2px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    color: #333;
    font-weight: 500;
    font-size: 15px;
}

.required {
    color: #ff4757;
    margin-left: 3px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 15px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: white;
}

.form-control:hover {
    border-color: #999;
}

.form-control:focus {
    outline: none;
    border-color: #ECF86E;
    box-shadow: 0 0 0 4px rgba(236, 248, 110, 0.1);
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

select.form-control {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%2F%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    padding-right: 40px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* Ошибки */
.is-invalid {
    border-color: #ff4757;
}

.error-message {
    display: block;
    margin-top: 5px;
    color: #ff4757;
    font-size: 13px;
}

/* Информация о билетах */
.tickets-info {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    text-align: center;
}

.ticket-stat {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.stat-label {
    color: #666;
    font-size: 14px;
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #333;
}

/* Изображение */
.current-image {
    display: flex;
    flex-direction: column;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 15px;
}

.current-image-label {
    color: #666;
    font-weight: 500;
}

.image-wrapper {
    display: inline-block;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.current-img {
    max-width: 300px;
    max-height: 200px;
    object-fit: cover;
    display: block;
}

.image-upload-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.image-preview {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.image-preview img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.form-text {
    display: block;
    margin-top: 8px;
    color: #999;
    font-size: 13px;
}

/* Кнопки */
.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
}

.btn-save, .btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 30px;
    border-radius: 50px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-save {
    background: #000;
    color: #ECF86E;
}

.btn-save:hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.btn-cancel {
    background: #f0f0f0;
    color: #666;
}

.btn-cancel:hover {
    background: #e4e4e4;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Адаптивность */

@media (max-width: 1024px) {
    .form-columns {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .form-container {
        padding: 30px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .tickets-info {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .form-container {
        padding: 25px;
    }
    
    .form-columns {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
        
    .form-actions {
        flex-direction: column;
    }
    
    .btn-save, .btn-cancel {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush