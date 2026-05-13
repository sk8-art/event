@extends('layouts.app')

@section('title', 'Концерты')

@php
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
@endphp

@section('content')

<div class="concerts-page">
    <!-- Календарь -->
    <div class="calendar-section">
        <div class="calendar-header">
            <h2 class="calendar-title">
                <span class="city-title">
                    @php
                        $selectedCity = session('selected_city', request('city', 'all'));
                    @endphp
                    @if($selectedCity && $selectedCity !== 'all')
                        Концерты • {{ $selectedCity }}
                    @else
                        Концерты
                    @endif
                </span>
            </h2>
            <div class="calendar-controls">
                <button class="calendar-nav prev" onclick="scrollCalendar(-7)">
                    ←
                </button>
                <button class="calendar-nav next" onclick="scrollCalendar(7)">
                    →
                </button>
            </div>
        </div>
        
        <div class="calendar-wrapper">
            <div class="calendar-scroll" id="calendarScroll">
                <div class="calendar-dates">
                    @php
                        $today = Carbon::today();
                        // Показываем даты на 3 месяца вперед
                        $endDate = $today->copy()->addMonths(3);
                        $currentDate = $today->copy();
                        $currentMonth = null;
                    @endphp
                    
                    @while($currentDate <= $endDate)
                        <!-- Разделитель месяцев -->
                        @if($currentDate->format('m-Y') != $currentMonth)
                            @if($currentMonth != null)
                                </div></div>
                            @endif
                            @php $currentMonth = $currentDate->format('m-Y'); @endphp
                            <div class="month-block">
                                <div class="month-title">{{ $currentDate->locale('ru')->isoFormat('MMMM YYYY') }}</div>
                                <div class="dates-row">
                        @endif
                        
                        <div class="date-cell {{ $currentDate->isToday() ? 'today' : '' }} 
                                  {{ request('date') == $currentDate->format('Y-m-d') ? 'selected' : '' }}"
                             onclick="selectDate('{{ $currentDate->format('Y-m-d') }}')">
                            <span class="date-number">{{ $currentDate->format('j') }}</span>
                            <span class="date-weekday">{{ $currentDate->locale('ru')->isoFormat('dd') }}</span>
                        </div>
                        
                        @php $currentDate->addDay(); @endphp
                    @endwhile
                    
                    @if($currentMonth != null)
                        </div></div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Кнопка "Все даты" -->
        <div class="calendar-footer">
            <button class="calendar-all-btn {{ !request('date') ? 'active' : '' }}" 
                    onclick="selectDate('')">
                Все даты
            </button>
        </div>
        
        <!-- Скрытая форма для отправки даты -->
        <form id="dateForm" action="{{ route('concerts') }}" method="GET" style="display: none;">
            <input type="hidden" name="date" id="selectedDate" value="{{ request('date') }}">
            <input type="hidden" name="location" value="{{ request('location') }}">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
        </form>
    </div>

    <!-- Список концертов -->
        <div class="events-grid" id="eventsContainer">
            @forelse($concerts as $concert)
                <div class="event-card" onclick="window.location='{{ route('events.show', $concert) }}'">
                    <!-- Фоновая картинка -->
                    <div class="event-image-bg" style="background-image: url('{{ asset('storage/' . $concert->image) }}');"></div>
                    
                    <!-- Кнопка избранного -->
                    <button class="favorite-btn {{ Auth::check() && Auth::user()->hasInFavorites($concert->id) ? 'active' : '' }}" 
                            data-event-id="{{ $concert->id }}"
                            onclick="event.stopPropagation(); toggleFavorite({{ $concert->id }}, this)" 
                            title="{{ Auth::check() && Auth::user()->hasInFavorites($concert->id) ? 'Удалить из избранного' : 'В избранное' }}">
                        
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
                        <h3 class="event-title">{{ $concert->title }}</h3>
                        
                        <!-- Дата, время и место -->
                        <div class="event-datetime">
                            <span class="datetime-item">{{ $concert->russian_date }}</span>
                            <span class="datetime-item">{{ $concert->formatted_time }}</span>
                            <span class="separator">•</span>
                            <span class="datetime-item">{{ $concert->location }}</span>
                        </div>
                        
                        <div class="price-buy">
                            <!-- Цена -->
                            <div class="event-meta">
                                <span class="event-price">от {{ number_format($concert->price, 0, ',', ' ') }} ₽</span>
                            </div>
                        
                            <!-- Кнопка Купить билет -->
                            <a href="{{ route('events.show', $concert) }}" class="event-button" onclick="event.stopPropagation();">
                                Купить билет
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-results">
                    <div class="no-results-content">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.9536 14.9458L21 21M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        <h3>Концерты не найдены</h3>
                        <p>Попробуйте изменить параметры поиска</p>
                        <a href="{{ route('concerts') }}" class="btn-reset-large">
                            <span>↺</span>
                            Сбросить фильтры
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Пагинация -->
        @if($concerts->hasPages())
            <div class="pagination-wrapper">
                {{ $concerts->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function scrollCalendar(offset) {
    const container = document.getElementById('calendarScroll');
    const scrollAmount = 500; // фиксированное значение для прокрутки на неделю
    container.scrollBy({
        left: offset > 0 ? scrollAmount : -scrollAmount,
        behavior: 'smooth'
    });
}

function selectDate(date) {
    document.getElementById('selectedDate').value = date;
    document.getElementById('dateForm').submit();
}

function toggleFavorite(eventId, button) {
    event.stopPropagation();
    
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

function clearLocation() {
    document.getElementById('location').value = '';
    document.getElementById('filterForm').submit();
}

// Анимация появления карточек
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
});

document.querySelectorAll('.event-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'all 0.5s ease';
    observer.observe(card);
});

// Переключение фильтров на мобильных
const filtersToggle = document.getElementById('filtersToggle');
const filtersSection = document.getElementById('filtersSection');

if (filtersToggle) {
    filtersToggle.addEventListener('click', () => {
        filtersSection.classList.toggle('show');
    });
}
</script>
@endpush

<style>
/* Календарь */
.calendar-section {
    margin-bottom: 40px;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.calendar-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.calendar-controls {
    display: flex;
    gap: 8px;
}

.calendar-nav {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1px solid #e0e0e0;
    background: white;
    color: #333;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.calendar-nav:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.calendar-nav:active {
    transform: translateY(0);
}

.calendar-wrapper {
    position: relative;
    width: 100%;
    overflow: hidden;
}

.calendar-scroll {
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 10px;
    scrollbar-width: thin;
    scrollbar-color: #ccc #f0f0f0;
    cursor: grab;
    scroll-behavior: smooth;
}

.calendar-scroll::-webkit-scrollbar {
    height: 6px;
}

.calendar-scroll::-webkit-scrollbar-track {
    background: #f0f0f0;
    border-radius: 10px;
}

.calendar-scroll::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.calendar-scroll::-webkit-scrollbar-thumb:hover {
    background: #999;
}

.calendar-dates {
    background: white;
    border-radius: 20px;
    padding: 20px;
    display: flex;
    flex-direction: row;
    gap: 30px;
    width: fit-content;
}

.month-block {
    display: flex;
    flex-direction: column;
    min-width: fit-content;
}

.month-title {
    font-size: 16px;
    font-weight: 600;
    color: #666;
    margin-bottom: 10px;
    padding-left: 5px;
}

.dates-row {
    display: flex;
    flex-direction: row;
    gap: 5px;
}

.date-cell {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 60px;
    padding: 10px 5px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f8f9fa;
}

.date-cell:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.date-cell.today {
    background: #e8e8e8;
    border: 1px solid #b1b1b1;
}

.date-cell.selected {
    background: #333;
    color: white;
}

.date-cell.selected .date-number {
    color: white;
}

.date-cell.selected .date-weekday {
    color: rgba(255,255,255,0.8);
}

.date-number {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    line-height: 1.2;
}

.date-weekday {
    font-size: 12px;
    color: #999;
    text-transform: uppercase;
    margin-top: 2px;
}

.calendar-footer {
    margin-top: 15px;
    display: flex;
    justify-content: center;
}

.calendar-all-btn {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    padding: 8px 25px;
    font-size: 14px;
    font-weight: 500;
    color: #666;
    cursor: pointer;
    transition: all 0.2s ease;
}

.calendar-all-btn:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.calendar-all-btn.active {
    background: #333;
    color: white;
    border-color: #333;
}

/* Остальные стили как на главной */
.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

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

.heart-icon {
    width: 28px;
    height: 28px;
    display: block;
}

.heart-path {
    stroke: white;
    fill: transparent;
    transition: fill 0.2s ease;
}

.favorite-btn.active .heart-path {
    fill: white;
    stroke: white;
}

.event-blur {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 50%;
    background: linear-gradient(
        to bottom,
        transparent 0%,
        rgba(255, 255, 255, 0) 15%,
        rgba(255, 255, 255, 0.05) 30%,
        rgba(255, 255, 255, 0.15) 45%,
        rgba(255, 255, 255, 0.30) 60%,
        rgba(255, 255, 255, 0.45) 75%,
        rgba(255, 255, 255, 0.55) 100%
    );
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    mask-image: linear-gradient(to bottom, transparent, black 25%);
    -webkit-mask-image: linear-gradient(to bottom, transparent, black 25%);
    z-index: 2;
    pointer-events: none;
}

.event-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 30px 20px 25px;
    z-index: 3;
    transform: translateY(0);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.event-card:hover .event-content {
    transform: translateY(-5px);
}

.event-title {
    font-size: 24px;
    font-weight: 700;
    line-height: 1.2;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    margin: 0;
}

.event-datetime {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
    font-size: 14px;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

.separator {
    color: #fff;
    font-weight: bold;
}

.price-buy {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.event-button {
    display: block;
    width: 55%;
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

.event-button:hover {
    background: #333;
    color: #ECF86E;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(35,35,35,0.4);
}

/* Фильтры */
.filters-wrapper {
    margin-bottom: 40px;
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.filters-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filters-toggle {
    display: none;
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 14px;
    cursor: pointer;
    align-items: center;
    gap: 8px;
}

.filters-section {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    font-size: 14px;
    font-weight: 500;
    color: #555;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-input,
.filter-select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: #333;
    box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
}

.filter-actions {
    display: flex;
    gap: 10px;
}

.btn-filter,
.btn-reset {
    padding: 12px 25px;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-filter {
    background: #333;
    color: white;
}

.btn-filter:hover {
    background: #000;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.btn-reset {
    background: #f8f9fa;
    color: #666;
    border: 1px solid #e0e0e0;
}

.btn-reset:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.results-header {
    margin-bottom: 20px;
}

.results-title {
    font-size: 20px;
    font-weight: 600;
    color: #333;
}

.results-count {
    color: #333;
}

.results-date {
    color: #666;
    font-size: 16px;
    font-weight: 400;
    margin-left: 8px;
}

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
    background: #292929;
}

.notification.error {
    background: #f44336;
}

@media (max-width: 768px) {
    .filters-toggle {
        display: flex;
    }
    
    .filters-section {
        display: none;
    }
    
    .filters-section.show {
        display: block;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        flex-direction: column;
    }
    
    .date-cell {
        min-width: 50px;
        padding: 8px 3px;
    }
    
    .date-number {
        font-size: 16px;
    }
    
    .date-weekday {
        font-size: 10px;
    }
    

    .event-blur {
        height: 40%;
    }
}
</style>
@endsection