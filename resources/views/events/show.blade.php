@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="event-detail-page">
    <!-- Герой с изображением -->
    <div class="event-hero">
        <div class="hero-image">
            <img src="{{ asset('storage/' . $event->image) }}" 
                 alt="{{ $event->title }}" 
                 class="hero-img">
            <div class="hero-overlay"></div>
        </div>
        
        <div class="hero-content">
            <h1 class="hero-title">{{ $event->title }}</h1>
            <div class="hero-breadcrumbs">
                <a href="{{ route($event->type === 'concert' ? 'concerts' : 'festivals') }}">
                    {{ $event->type === 'concert' ? 'Концерт' : 'Фестиваль' }}
                </a>
            </div>
            
            
            <div class="hero-meta">
                <div class="hero-meta-item">
                    <span>{{ $event->russian_date }} • {{ $event->formatted_time }}</span>
                </div>
                <div class="hero-meta-item">
                    <span>{{ $event->location }}, {{ $event->address }}</span>
                </div>
            </div>
            
            @if($event->startsSoon(24))
                <span class="hero-badge hot"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Baseline-local-fire-department SVG Icon</title><path fill="currentColor" d="m12 12.9l-2.13 2.09c-.56.56-.87 1.29-.87 2.07C9 18.68 10.35 20 12 20s3-1.32 3-2.94c0-.78-.31-1.52-.87-2.07z"/><path fill="currentColor" d="m16 6l-.44.55C14.38 8.02 12 7.19 12 5.3V2S4 6 4 13c0 2.92 1.56 5.47 3.89 6.86c-.56-.79-.89-1.76-.89-2.8c0-1.32.52-2.56 1.47-3.5L12 10.1l3.53 3.47c.95.93 1.47 2.17 1.47 3.5c0 1.02-.31 1.96-.85 2.75c1.89-1.15 3.29-3.06 3.71-5.3c.66-3.55-1.07-6.9-3.86-8.52"/></svg> СКОРО</span>
            @endif
            
            @if($event->status === 'cancelled')
                <span class="hero-badge cancelled"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 16 16"><title>Cross SVG Icon</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m11.25 4.75l-6.5 6.5m0-6.5l6.5 6.5"/></svg> ОТМЕНЕНО</span>
            @endif
        </div>
    </div>

    <!-- Основной контент -->
    <div class="event-main-content">
        <!-- Описание мероприятия -->
        <div class="event-description-section">
            <h2>О мероприятии</h2>
            <div class="description-content">
                {{ $event->description }}
            </div>
        </div>

        <!-- Дополнительные изображения -->
        @if($event->images)
        <div class="event-gallery">
            <h2>Галерея</h2>
            <div class="image-thumbnails">
                @foreach($event->images as $image)
                    <img src="{{ asset('storage/' . $image) }}" 
                         alt=""
                         class="thumbnail"
                         onclick="openGallery(this.src)">
                @endforeach
            </div>
        </div>
        @endif

        <!-- Бронирование билетов -->
        @auth
        <div class="booking-section" id="booking">
            <h2>Выберите тип билета</h2>
            
            @if($event->canBuyTickets())
                @if($event->ticketTypes->where('available', '>', 0)->count() > 0)
                    <form action="{{ route('orders.store', $event) }}" method="POST" class="booking-form">
                        @csrf
                        
                        <div class="ticket-types-grid">
                            @foreach($event->ticketTypes as $ticketType)
                                @php
                                    $isAvailable = $ticketType->available > 0;
                                @endphp
                                
                                <div class="ticket-type-option {{ !$isAvailable ? 'unavailable' : '' }}" 
                                     data-available="{{ $isAvailable ? 'true' : 'false' }}"
                                     onclick="{{ $isAvailable ? 'selectTicketType(' . $ticketType->id . ')' : '' }}">
                                    
                                    @if($isAvailable)
                                        <input type="radio" 
                                               name="ticket_type_id" 
                                               value="{{ $ticketType->id }}" 
                                               id="ticket_{{ $ticketType->id }}"
                                               data-price="{{ $ticketType->price }}"
                                               data-name="{{ $ticketType->name }}"
                                               data-available="{{ $ticketType->available }}"
                                               style="display: none;">
                                    @endif
                                    
                                    <label for="ticket_{{ $ticketType->id }}" class="ticket-type-label">
                                        <div class="ticket-type-header">
                                            <div class="ticket-type-name">{{ $ticketType->name }}</div>
                                            @if(!$isAvailable)
                                                <div class="ticket-type-badge">Нет в наличии</div>
                                            @elseif($ticketType->available < 10)
                                                <div class="ticket-type-badge warning">Осталось {{ $ticketType->available }}</div>
                                            @endif
                                        </div>
                                        
                                        <div class="ticket-type-price">{{ number_format($ticketType->price, 0, ',', ' ') }} ₽</div>
                                        
                                        <div class="ticket-type-available">
                                            @if($isAvailable)
                                                
                                            @else
                                                Билеты закончились
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="booking-controls">
                            <div class="form-group">
                                <label for="quantity">Количество билетов:</label>
                                <input type="number" 
                                       name="quantity" 
                                       id="quantity" 
                                       min="1" 
                                       max="10" 
                                       value="1" 
                                       class="form-control"
                                       onchange="updateTotal()">
                            </div>

                            <div class="selected-ticket-info" id="selectedTicketInfo" style="display: none;">
                                <p>Выбран: <span id="selectedTicketName"></span></p>
                                <p>Цена за билет: <span id="selectedTicketPrice"></span></p>
                                <p>Доступно: <span id="selectedTicketAvailable"></span> билетов</p>
                            </div>

                            <div class="total-price" id="totalPriceContainer" style="display: none;">
                                <span>Итого к оплате:</span>
                                <span id="totalPrice">0 ₽</span>
                            </div>

                            <button type="submit" class="btn-book" id="submitBtn" disabled>
                                Забронировать
                            </button>
                        </div>
                    </form>
                @else
                    <div class="no-tickets">
                        <p>🎫 Нет доступных типов билетов</p>
                    </div>
                @endif
            @else
                <div class="no-tickets">
                    @if($event->status === 'cancelled')
                        <p>Мероприятие отменено</p>
                    @elseif($event->isFinished())
                        <p>Мероприятие завершено</p>
                    @elseif($event->available_tickets === 0)
                        <p>Все билеты проданы</p>
                    @else
                        <p>Билеты на это мероприятие уже нельзя купить</p>
                    @endif
                </div>
            @endif
        </div>
        @else
            <div class="login-prompt">
                <p>Чтобы забронировать билеты, <a href="{{ route('login') }}">войдите</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a></p>
            </div>
        @endauth

        <!-- Кнопка редактирования для организатора -->
        @auth
            @if(auth()->user()->isOrganizer() && auth()->user()->id === $event->organizer_id)
                <div class="action-buttons">
                    <a href="{{ route('organizer.edit', $event) }}" class="btn-edit-event">
                        <span class="btn-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75zM20.71 7.04a.996.996 0 0 0 0-1.41l-2.34-2.34a.996.996 0 0 0-1.41 0l-1.83 1.83l3.75 3.75z"/></svg></span>
                        Редактировать
                    </a>
                </div>
            @endif
        @endauth

        <!-- Популярные мероприятия -->
        @if(isset($popularEvents) && $popularEvents->count() > 0)
        <div class="popular-events-section">
            <h2 class="section-title">Популярно</h2>
            <div class="popular-events-grid">
                @foreach($popularEvents as $popular)
                    <div class="popular-event-card" onclick="window.location='{{ route('events.show', $popular) }}'">
                        <div class="popular-event-image">
                            <img src="{{ asset('storage/' . $popular->image) }}" alt="{{ $popular->title }}">
                            @if($popular->startsSoon(24))
                                <span class="event-badge hot"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Baseline-local-fire-department SVG Icon</title><path fill="currentColor" d="m12 12.9l-2.13 2.09c-.56.56-.87 1.29-.87 2.07C9 18.68 10.35 20 12 20s3-1.32 3-2.94c0-.78-.31-1.52-.87-2.07z"/><path fill="currentColor" d="m16 6l-.44.55C14.38 8.02 12 7.19 12 5.3V2S4 6 4 13c0 2.92 1.56 5.47 3.89 6.86c-.56-.79-.89-1.76-.89-2.8c0-1.32.52-2.56 1.47-3.5L12 10.1l3.53 3.47c.95.93 1.47 2.17 1.47 3.5c0 1.02-.31 1.96-.85 2.75c1.89-1.15 3.29-3.06 3.71-5.3c.66-3.55-1.07-6.9-3.86-8.52"/></svg> Скоро</span>
                            @endif
                        </div>
                        <div class="popular-event-content">
                            <h4 class="popular-event-title">{{ $popular->title }}</h4>
                            <div class="popular-event-meta">
                                <span>{{ $popular->russian_date }}</span>
                                <span class="separator">•</span>
                                <span>{{ $popular->location }}</span>
                            </div>
                            <div class="popular-event-footer">
                                <span class="popular-event-price">
                                    от {{ number_format($popular->price, 0, ',', ' ') }} ₽
                                </span>
                                <span class="popular-event-views">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    {{ $popular->views }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Герой с изображением */
.event-hero {
    position: relative;
    width: 100vw;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
    margin-top: -90px; /* Наезжаем на хедер */
    margin-bottom: 40px;
    height: 500px;
    overflow: hidden;
}

.hero-image {
    position: relative;
    width: 100%;
    height: 160%;
}

.hero-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.7));
}

.hero-content {
    position: absolute;
    bottom: 40px;
    left: 0;
    right: 0;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    color: white;
    z-index: 2;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.hero-breadcrumbs {
    display: flex;
    gap: 8px;
    color: rgba(255,255,255,0.8);
    font-size: 14px;
}

.hero-breadcrumbs a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: color 0.3s;
}

.hero-breadcrumbs a:hover {
    color: #ECF86E;
}

.hero-breadcrumbs .current {
    color: white;
    font-weight: 500;
}

.hero-title {
    font-size: 48px;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.hero-meta {
    display: flex;
    gap: 20px;
    font-size: 16px;
}

.hero-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.hero-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
}

.hero-badge.hot {
    display: flex;
    gap: 5px;
    background: #fffe23;
    color: #000000;
    width: fit-content;
    align-items: center;
}

.hero-badge.cancelled {
    background: #000000ff;
    color: white;
    width: fit-content;
    display: flex;
    align-items: center; 
    gap: 5px;
}

/* Основной контент */
.event-main-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Описание */
.event-description-section {
    margin-bottom: 40px;
}

.event-description-section h2 {
    font-size: 28px;
    margin-bottom: 20px;
    color: #333;
}

.description-content {
    line-height: 1.8;
    color: #555;
}

/* Галерея */
.event-gallery {
    margin-bottom: 40px;
}

.event-gallery h2 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
}

.image-thumbnails {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.thumbnail {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.thumbnail:hover {
    transform: scale(1.05);
    border-color: #ECF86E;
}

/* Бронирование билетов */
.booking-section {
    margin: 40px 0;
    padding: 30px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.booking-section h2 {
    font-size: 28px;
    color: #333;
}

.ticket-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.ticket-type-option {
    position: relative;
    cursor: pointer;
}

.ticket-type-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.ticket-type-label {
    display: flex;
    flex-direction: column;
    padding: 25px;
    border: 2px solid #dee2e6;
    border-radius: 12px;
    transition: all 0.3s;
    background: white;
    height: 100%;
}

.ticket-type-option:not(.unavailable):hover .ticket-type-label {
    border-color: #ECF86E;
    box-shadow: 0 4px 12px rgba(236, 248, 110, 0.2);
}

.ticket-type-option.selected .ticket-type-label {
    border-color: #ECF86E;
    background: #fefff3;
    box-shadow: 0 4px 12px rgba(236, 248, 110, 0.2);
}

.ticket-type-option.unavailable {
    cursor: not-allowed;
    opacity: 0.7;
}

.ticket-type-option.unavailable .ticket-type-label {
    background: #f5f5f5;
    border-color: #d0d0d0;
    pointer-events: none;
}

.ticket-type-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.ticket-type-name {
    font-size: 18px;
    font-weight: 600;
}

.ticket-type-badge {
    font-size: 11px;
    font-weight: 600;
    color: #000000ff;
    background: #f44336;
    padding: 4px 8px;
    border-radius: 20px;
}

.ticket-type-badge.warning {
    background: #ECF86E;
}

.ticket-type-price {
    font-size: 24px;
    font-weight: 700;
    color: #000;
    margin-bottom: 10px;
}


/* Контролы бронирования */
.booking-controls {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
    max-width: 400px;
    margin-top: 20px;
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.form-group label {
    display: block;
    font-weight: 500;
    color: #555;
}

.form-control {
    width: 100px;
    padding: 8px 12px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 16px;
}

.selected-ticket-info {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 8px;
    margin: 15px 0;
}

.selected-ticket-info p {
    margin: 5px 0;
}

.total-price {
    font-size: 18px;
    font-weight: 700;
    margin: 15px 0;
    text-align: right;
}

.btn-book:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    padding: 12px 24px;
    border-radius: 8px;
    border: none;
    background: #6c757d;
    color: white;
}

.btn-book:not(:disabled) {
    background: #000;
    color: #ECF86E;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-book:not(:disabled):hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

/* Кнопка редактирования */
.btn-edit-event {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #666;
    color: white;
    padding: 8px 20px;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-edit-event:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

/* Популярные мероприятия */
.popular-events-section {
    margin: 60px 0 40px;
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.section-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
}

.popular-events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 25px;
}

.popular-event-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s;
    cursor: pointer;
    border: 1px solid #f0f0f0;
}

.popular-event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.popular-event-image {
    position: relative;
    width: 100%;
    height: 140px;
    overflow: hidden;
}

.popular-event-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.popular-event-card:hover .popular-event-image img {
    transform: scale(1.05);
}

.event-badge.hot {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ECF86E;
    color: #000;
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    z-index: 2;
    width: fit-content;
    display: flex;
    align-items: center;
}

.popular-event-content {
    padding: 15px;
}

.popular-event-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 8px;
    color: #333;
}

.popular-event-meta {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: #666;
    margin-bottom: 8px;
}

.popular-event-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.popular-event-price {
    font-size: 16px;
    font-weight: 700;
    color: #000;
}

.popular-event-views {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #999;
    font-size: 12px;
}

/* Адаптивность */
@media (max-width: 768px) {
    .event-hero {
        height: 400px;
        margin-top: -90px;
    }
    .hero-content {
        gap: 8px;
    }
    .hero-title {
        font-size: 32px;
    }
    
    .hero-meta {
        flex-direction: column;
        gap: 10px;
    }
    
}
</style>

<script>
function openGallery(src) {
    // Функция для открытия галереи
    console.log('Open gallery:', src);
}

// Остальные функции остаются без изменений
window.selectTicketType = function(id) {
    console.log('Выбран билет с ID:', id);
    
    document.querySelectorAll('.ticket-type-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    const selectedOption = event.currentTarget;
    selectedOption.classList.add('selected');
    
    const radio = document.getElementById('ticket_' + id);
    if (radio) {
        radio.checked = true;
        const price = parseFloat(radio.dataset.price);
        const name = radio.dataset.name;
        const available = radio.dataset.available;
        
        const nameSpan = document.getElementById('selectedTicketName');
        const priceSpan = document.getElementById('selectedTicketPrice');
        const availableSpan = document.getElementById('selectedTicketAvailable');
        const infoDiv = document.getElementById('selectedTicketInfo');
        const totalContainer = document.getElementById('totalPriceContainer');
        const submitBtn = document.getElementById('submitBtn');
        const quantityInput = document.getElementById('quantity');
        
        if (nameSpan) nameSpan.textContent = name;
        if (priceSpan) priceSpan.textContent = new Intl.NumberFormat('ru-RU').format(price) + ' ₽';
        if (availableSpan) availableSpan.textContent = available;
        if (infoDiv) infoDiv.style.display = 'block';
        if (totalContainer) totalContainer.style.display = 'block';
        if (submitBtn) submitBtn.disabled = false;
        
        if (quantityInput) {
            const max = Math.min(10, parseInt(available));
            quantityInput.max = max;
            if (parseInt(quantityInput.value) > max) {
                quantityInput.value = max;
            }
        }
        
        updateTotal();
    }
}

window.updateTotal = function() {
    const quantityInput = document.getElementById('quantity');
    const totalSpan = document.getElementById('totalPrice');
    const selectedRadio = document.querySelector('input[name="ticket_type_id"]:checked');
    
    if (!quantityInput || !totalSpan) return;
    
    if (selectedRadio) {
        const price = parseFloat(selectedRadio.dataset.price);
        const quantity = parseInt(quantityInput.value) || 1;
        const total = price * quantity;
        totalSpan.textContent = new Intl.NumberFormat('ru-RU').format(total) + ' ₽';
    } else {
        totalSpan.textContent = '0 ₽';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('input', updateTotal);
    }
});
</script>
@endsection