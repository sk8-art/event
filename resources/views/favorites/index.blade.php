@extends('layouts.app')

@section('title', 'Избранное')

@section('content')
<div class="favorites-page">
    <div class="page-header">
        <h1 class="page-title">Избранное</h1>
    </div>

    @if($favorites->count() > 0)
        <div class="favorites-grid">
            @foreach($favorites as $event)
                <div class="favorite-card" onclick="window.location='{{ route('events.show', $event) }}'">
                    <!-- Фоновая картинка -->
                    <div class="favorite-card-image-bg" style="background-image: url('{{ asset('storage/' . $event->image) }}');"></div>
                    
                    <!-- Кнопка удаления -->
                    <button class="remove-favorite" onclick="event.stopPropagation(); removeFromFavorites({{ $event->id }})" title="Удалить из избранного">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    </button>
                    
                    <!-- Плавное размытие -->
                    <div class="favorite-card-blur"></div>
                    
                    <!-- Контент -->
                    <div class="favorite-card-content">
                        <h3 class="favorite-card-title">{{ $event->title }}</h3>
                        
                        <div class="favorite-card-info">
                            <span class="info-item">{{ $event->russian_date }}</span>
                            <span class="info-separator">•</span>
                            <span class="info-item">{{ $event->formatted_time }}</span>
                            <span class="info-separator">•</span>
                            <span class="info-item">{{ $event->location }}</span>
                        </div>
                        
                        <div class="favorite-card-footer">
                            <span class="favorite-card-price">
                                @if($event->price > 0)
                                    {{ number_format($event->price, 0, ',', ' ') }} ₽
                                @else
                                    Бесплатно
                                @endif
                            </span>
                            
                            <span class="favorite-card-view">Подробнее</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="no-favorites">
            <div class="no-favorites-content">
                <svg class="no-favorites-icon" width="64" height="64" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 42L6.3 24.3C3.9 21.9 3 18.7 3 15.5C3 9 8.5 3.5 15 3.5C18.5 3.5 21.9 5.2 24 8C26.1 5.2 29.5 3.5 33 3.5C39.5 3.5 45 9 45 15.5C45 18.7 44.1 21.9 41.7 24.3L24 42Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                </svg>
                <h3>У вас пока нет избранных мероприятий</h3>
                <p>Добавляйте мероприятия в избранное, чтобы не пропустить ничего интересного</p>
                <a href="{{ route('concerts') }}" class="btn-browse">Посмотреть концерты</a>
            </div>
        </div>
    @endif
</div>

<script>
function removeFromFavorites(eventId) {
    fetch('/favorites/remove/' + eventId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Удаляем карточку из DOM
            const card = document.querySelector(`.favorite-card[data-event-id="${eventId}"]`);
            if (card) {
                card.remove();
            }
            
            // Показываем уведомление
            showNotification('Удалено из избранного', 'success');
            
            // Если не осталось карточек, показываем пустое состояние
            if (document.querySelectorAll('.favorite-card').length === 0) {
                window.location.reload();
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Произошла ошибка', 'error');
    });
}
</script>

@endsection

@push('styles')
<style>
.favorites-page {
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
    padding-bottom: 8px;
}

.page-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: #000000ff;
    border-radius: 2px;
}

.page-subtitle {
    color: #666;
    font-size: 18px;
}

/* Сетка избранного */
.favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

/* Карточка как на главной */
.favorite-card {
    position: relative;
    border-radius: 30px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    aspect-ratio: 3.6 / 4;
    cursor: pointer;
}

.favorite-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.favorite-card:hover .favorite-card-image-bg {
    transform: scale(1.05);
}

.favorite-card-image-bg {
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

/* Кнопка удаления */
.remove-favorite {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 36px;
    height: 36px;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    opacity: 0.7;
    transition: all 0.2s ease;
}

.remove-favorite:hover {
    opacity: 1;
    background: #ECF86E;
    color: #000; 
    transform: rotate(90deg);
}

.remove-favorite svg {
    width: 16px;
    height: 16px;
}

/* Плавное размытие */
.favorite-card-blur {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 47%;
    background: linear-gradient(
        to bottom,
        transparent 0%,
        rgba(255, 255, 255, 0) 15%,
        rgba(255, 255, 255, 0.05) 30%,
        rgba(255, 255, 255, 0.14) 45%,
        rgba(255, 255, 255, 0.39) 60%,
        rgba(255, 255, 255, 0.41) 75%,
        rgba(255, 255, 255, 0.54) 100%
    );
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    mask-image: linear-gradient(to bottom, transparent, black 25%);
    -webkit-mask-image: linear-gradient(to bottom, transparent, black 25%);
    z-index: 2;
    pointer-events: none;
}

/* Контент */
.favorite-card-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 25px 20px;
    z-index: 3;
    color: white;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.favorite-card-title {
    font-size: 22px;
    font-weight: 700;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    margin: 0;
    line-height: 1.2;
}

.favorite-card-info {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9);
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.info-separator {
    color: rgba(255, 255, 255, 0.6);
    font-weight: bold;
}

.favorite-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.favorite-card-price {
    font-size: 13px;
    font-weight: 700;
    color: #000;
    padding: 6px 14px;
    border-radius: 30px;
    background: #ECF86E;
    backdrop-filter: blur(20px);
}

.favorite-card-view {
    display: block;
    width: 45%;
    background: #fff;
    color: #000;
    border: none;
    border-radius: 35px;
    padding: 12px;
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

.event-favorite-card-view:hover {
    background: #333;
    color: #ECF86E;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(35,35,35,0.4);
}

.favorite-card:hover {
    color: #ECF86E;
    transform: translateX(1.5px);
}

/* Пустое состояние */
.no-favorites {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.no-favorites-content {
    max-width: 400px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.no-favorites-icon {
    width: 64px;
    height: 64px;
    color: #ddd;
}

.no-favorites h3 {
    color: #333;
    font-size: 24px;
    margin: 0;
}

.no-favorites p {
    color: #666;
    margin: 0;
}

.btn-browse {
    display: inline-block;
    padding: 12px 30px;
    background: #000;
    color: #ECF86E;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
}

.btn-browse:hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Адаптивность */
@media (max-width: 768px) {
    .favorites-page {
        padding: 30px 15px;
    }
    
    .page-title {
        font-size: 28px;
    }
    
    .page-subtitle {
        font-size: 16px;
    }
    
    .favorites-grid {
        gap: 20px;
    }
    
    .favorite-card-title {
        font-size: 20px;
    }
    
    .favorite-card-content {
        padding: 20px 15px;
    }
    
    .favorite-card-blur {
        height: 40%;
    }
    
    .favorite-card-price {
        font-size: 18px;
    }
}
</style>
@endpush