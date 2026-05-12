@extends('layouts.app')

@section('title', 'Главная')

@php
use Illuminate\Support\Facades\Auth;
@endphp

@section('content')
<div class="events-section">
    <h2>Ближайшие мероприятия</h2>
    <div class="events-grid">
        @if(isset($events) && $events->count() > 0)
            @foreach($events as $event)
                <div class="event-card" onclick="window.location='{{ route('events.show', $event) }}'">
                    <!-- Фоновая картинка на всю карточку -->
                    <div class="event-image-bg" style="background-image: url('{{ asset('storage/' . $event->image) }}');"></div>
                    
                    <!-- Кнопка избранного -->
                    <button class="favorite-btn {{ Auth::check() && Auth::user()->hasInFavorites($event->id) ? 'active' : '' }}" 
                            data-event-id="{{ $event->id }}"
                            onclick="event.stopPropagation(); toggleFavorite({{ $event->id }}, this)" 
                            title="{{ Auth::check() && Auth::user()->hasInFavorites($event->id) ? 'Удалить из избранного' : 'В избранное' }}">
                        
                        <svg class="heart-icon" 
                            xmlns="http://www.w3.org/2000/svg" 
                            width="28" 
                            height="28" 
                            viewBox="0 0 48 48">
                            <title>Like SVG Icon</title>
                            <path class="heart-path" 
                                fill="none" 
                                stroke="currentColor" 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="4" 
                                d="M15 8C8.925 8 4 12.925 4 19c0 11 13 21 20 23.326C31 40 44 30 44 19c0-6.075-4.925-11-11-11c-3.72 0-7.01 1.847-9 4.674A10.987 10.987 0 0 0 15 8"/>
                        </svg>
                    </button>
                    
                    <!-- Плавное размытие -->
                    <div class="event-blur"></div>
                    
                    <!-- Контент поверх -->
                    <div class="event-content">
                        <h3 class="event-title">{{ $event->title }}</h3>
                        
                        <!-- Дата, время и место с точками-разделителями -->
                        <div class="event-datetime">
                            <span class="datetime-item">{{ $event->russian_date }}</span>
                            <span class="datetime-item">{{ $event->formatted_time }}</span>
                            <span class="separator">•</span>
                            <span class="datetime-item">{{ $event->location }}</span>
                        </div>
                        <div class="price-buy">
                            <!-- Цена -->
                            <div class="event-meta">
                                <span class="event-price">от {{ number_format($event->price, 0, ',', ' ') }} ₽</span>
                            </div>
                        
                            <!-- Кнопка Купить билет -->
                            <a href="{{ route('events.show', $event) }}" class="event-button" onclick="event.stopPropagation();">
                                Купить билет
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="no-events">Мероприятий пока нет</p>
        @endif
    </div>
</div>

<!-- Функции для избранного -->
@push('scripts')
<script>
function toggleFavorite(eventId, button) {
    @auth
        const isAdding = !button.classList.contains('active');
        const url = isAdding ? '/favorites/add/' + eventId : '/favorites/remove/' + eventId;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (isAdding) {
                    button.classList.add('active');
                    button.title = 'Удалить из избранного';
                } else {
                    button.classList.remove('active');
                    button.title = 'В избранное';
                }
                
                showNotification(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Произошла ошибка', 'error');
        });
    @else
        window.location.href = '{{ route('login') }}';
    @endauth
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
</script>
@endpush

<style>
/* Основные стили секции */
.events-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.events-section h2 {
    font-size: 32px;
    color: #333;
    text-align: center;
    margin-bottom: 20px;
}

/* Сетка карточек */
.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

/* Карточка мероприятия */
.event-card {
    position: relative;
    border-radius: 30px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    aspect-ratio: 3.6 / 4;
    cursor: pointer;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.event-card:hover .event-image-bg {
    transform: scale(1.05);
}

/* Фоновая картинка */
.event-image-bg {
    filter: brightness(0.8);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    transition: transform 0.5s ease;
    z-index: 1;
}

/* Кнопка избранного */
.favorite-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 8px;
    z-index: 10;
    transition: transform 0.2s ease;
}

.favorite-btn:hover {
    transform: scale(1.1);
}

.favorite-btn:active {
    transform: scale(0.95);
}

.heart-icon {
    width: 28px;
    height: 28px;
    display: block;
}

.heart-path {
    stroke: white;
    fill: transparent;
    transition: fill 0.2s ease, stroke 0.2s ease;
}

.favorite-btn.active .heart-path {
    fill: white;
    stroke: white;
}

/* Плавное размытие */
.event-blur {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 50%;
    background: linear-gradient(
        to bottom,
        transparent 0%,
        rgba(144, 144, 144, 0) 15%,
        rgba(136, 136, 136, 0.05) 30%,
        rgba(144, 144, 144, 0.14) 45%,
        rgba(132, 132, 132, 0.39) 60%,
        rgba(180, 180, 180, 0.41) 75%,
        rgba(203, 203, 203, 0.54) 100%
    );
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    mask-image: linear-gradient(to bottom, transparent, black 25%);
    -webkit-mask-image: linear-gradient(to bottom, transparent, black 25%);
    z-index: 2;
    pointer-events: none;
}

/* Контент поверх */
.event-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 30px 20px 25px 20px;
    z-index: 3;
    color: #111;
    transform: translateY(0);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
}

.event-card:hover .event-content {
    transform: translateY(-5px);
}

.event-title {
    font-size: 24px;
    font-weight: 700;
    line-height: 1.2;
    color: #ffffff;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

.event-datetime {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
    font-size: 14px;
    color: #ffffff;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

.datetime-item {
    font-weight: 500;
}

.separator {
    color: #ffffff;
    font-weight: bold;
}

.event-button {
    display: block;
    width: 55%;
    background: #fff;
    color: #000;
    border: none;
    border-radius: 35px;
    padding: 14px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    text-decoration: none;
    z-index: 5;
    position: relative;
}

.price-buy {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.event-button:hover {
    background: #333333;
    color: #ECF86E;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(35, 35, 35, 0.4);
}

.no-events {
    grid-column: 1 / -1;
    text-align: center;
    padding: 50px;
    background: #f8f9fa;
    border-radius: 10px;
    color: #666;
    font-size: 16px;
}

/* Уведомления */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 24px;
    border-radius: 8px;
    color: white;
    font-size: 14px;
    z-index: 9999;
    transform: translateX(120%);
    transition: transform 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.notification.show {
    transform: translateX(0);
}

.notification.success {
    background: #4caf50;
}

.notification.error {
    background: #f44336;
}

/* Адаптивность */
@media (max-width: 768px) {
    .events-section {
        padding: 20px 15px;
    }
    
    .events-section h2 {
        font-size: 24px;
    }
    
    .events-grid {
        gap: 20px;
    }
    
    .event-title {
        font-size: 20px;
    }
    
    .event-content {
        padding: 20px 15px 20px 15px;
    }
    
    .event-blur {
        height: 65%;
    }
    
    .event-price {
        font-size: 11px;
        padding: 5px 12px;
    }
    
    .event-datetime {
        font-size: 13px;
    }
    
    .event-button {
        padding: 12px;
        font-size: 13px;
        width: 60%;
    }
}
</style>
@endsection