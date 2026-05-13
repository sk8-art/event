@extends('layouts.app')

@section('title', 'Поиск: ' . $query)

@section('content')
<div class="search-page">
    
    @if($events->count() > 0)
        <!-- Фильтры результатов -->
        <div class="search-filters">
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">Все</button>
                <button class="filter-tab" data-filter="concert">Концерты</button>
                <button class="filter-tab" data-filter="festival">Фестивали</button>
                <button class="filter-tab" data-filter="other">Другое</button>
            </div>
            
            <div class="sort-options">
                <label>Сортировать:</label>
                <select id="search-sort" class="sort-select">
                    <option value="relevance">По релевантности</option>
                    <option value="date_asc">Сначала ближайшие</option>
                    <option value="date_desc">Сначала поздние</option>
                    <option value="price_asc">Сначала дешевле</option>
                    <option value="price_desc">Сначала дороже</option>
                </select>
            </div>
        </div>

        <!-- Результаты -->
        <div class="events-grid" id="searchResults">
            @foreach($events as $event)
                <div class="event-card" 
                data-type="{{ $event->type }}" data-date="{{ $event->date }}" 
                data-price="{{ $event->price }}"
                onclick="window.location='{{ route('events.show', $event) }}'">
                    <!-- Фоновая картинка -->
                    <div class="event-image-bg" style="background-image: url('{{ asset('storage/' . $event->image) }}');"></div>
                    
                    <!-- Плавное размытие -->
                    <div class="event-blur"></div>
                    
                    <!-- Контент поверх -->
                    <div class="event-content">
                        <!-- Бейджи -->
                        <div class="event-badges-top">
                            
                            @if($event->startsSoon(24))
                                <span class="badge hot">Скоро</span>
                            @endif
                            
                        </div>
                        
                        <h3 class="event-title">
                            {!! highlightText($event->title, $query) !!}
                        </h3>
                        
                        <!-- Дата, время и место -->
                        <div class="event-datetime">
                            <span class="datetime-item">{{ $event->russian_date }}</span>
                            <span class="separator">•</span>
                            <span class="datetime-item">{{ $event->formatted_time }}</span>
                            <span class="separator">•</span>
                            <span class="datetime-item">
                                @if($selectedCity && $selectedCity !== 'all' && stripos($event->location, $selectedCity) !== false)
                                    <span class="city-highlight">{!! highlightText($event->location, $selectedCity) !!}</span>
                                @else
                                    {!! highlightText($event->location, $query) !!}
                                @endif
                            </span>
                        </div>
                        
                        <div class="price-buy">
                            <!-- Цена и совпадение -->
                            <div class="price-section">
                                <span class="event-price">
                                    @if($event->price > 0)
                                        от {{ number_format($event->price, 0, ',', ' ') }} ₽
                                    @else
                                        Бесплатно
                                    @endif
                                </span>
                                
                            </div>
                        
                            <!-- Кнопка -->
                            <a href="{{ route('events.show', $event) }}" class="event-button" onclick="event.stopPropagation();">
                                Подробнее
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        <div class="pagination-wrapper">
            {{ $events->withQueryString()->links() }}
        </div>
    @else
        <div class="no-results-found">
            <div class="no-results-content">
                <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 512 512"><title>Search SVG Icon</title><path fill="currentColor" d="M456.69 421.39L362.6 327.3a173.81 173.81 0 0 0 34.84-104.58C397.44 126.38 319.06 48 222.72 48S48 126.38 48 222.72s78.38 174.72 174.72 174.72A173.81 173.81 0 0 0 327.3 362.6l94.09 94.09a25 25 0 0 0 35.3-35.3M97.92 222.72a124.8 124.8 0 1 1 124.8 124.8a124.95 124.95 0 0 1-124.8-124.8"/></svg>
                <h2>По запросу "{{ $query }}" ничего не найдено</h2>
                <p>Попробуйте изменить поисковый запрос</p>
                
                <div class="search-suggestions">
                    <h4>Возможно, вы искали:</h4>
                    <div class="suggestions-list">
                        <a href="{{ route('concerts') }}" class="suggestion-tag">Концерты</a>
                        <a href="{{ route('festivals') }}" class="suggestion-tag">Фестивали</a>
                        <a href="{{ route('home') }}" class="suggestion-tag">Главная</a>
                    </div>
                </div>
                
                <a href="{{ route('home') }}" class="btn-home">
                    На главную
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Фильтрация по типу
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            const cards = document.querySelectorAll('.event-card');
            
            cards.forEach(card => {
                if (filter === 'all' || card.dataset.type === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Сортировка
document.getElementById('search-sort')?.addEventListener('change', function() {
    const sort = this.value;
    const container = document.getElementById('searchResults');
    
    // Получаем все карточки и преобразуем в массив
    const cards = Array.from(document.querySelectorAll('.event-card'));
    
    // Сортируем карточки
    cards.sort((a, b) => {
        // Получаем цены из data-атрибутов
        const priceA = parseFloat(a.dataset.price) || 0;
        const priceB = parseFloat(b.dataset.price) || 0;
        
        // Для сортировки по цене
        if (sort === 'price_asc') {
            return priceA - priceB;
        }
        if (sort === 'price_desc') {
            return priceB - priceA;
        }
        
        // Для сортировки по дате
        if (sort === 'date_asc' || sort === 'date_desc') {
            // Получаем дату из data-атрибута
            const dateA = a.dataset.date;
            const dateB = b.dataset.date;
            
            if (!dateA || !dateB) return 0;
            
            // Преобразуем в timestamp
            const timeA = new Date(dateA).getTime();
            const timeB = new Date(dateB).getTime();
            
            if (isNaN(timeA) || isNaN(timeB)) return 0;
            
            if (sort === 'date_asc') {
                return timeA - timeB; // Сначала ближайшие
            } else {
                return timeB - timeA; // Сначала поздние
            }
        }
        
        // По умолчанию (relevance) - не меняем порядок
        return 0;
    });
    
    // Очищаем контейнер и добавляем отсортированные карточки
    container.innerHTML = '';
    cards.forEach(card => container.appendChild(card));
});

    // Анимация появления
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
</script>
@endpush

@php
function highlightText($text, $query) {
    if (!$query) return $text;
    return preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $text);
}
@endphp

<style>
/* Страница поиска - стили в вашем дизайне */
.search-page {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}


.hero-title {
    font-size: 48px;
    font-weight: 700;
    color: #ECF86E;
    margin-bottom: 30px;
}

.search-large-form {
    max-width: 600px;
    margin: 0 auto 20px;
}

.search-wrapper {
    display: flex;
    align-items: center;
    background: white;
    border-radius: 50px;
    padding: 5px;
}

.search-icon {
    padding: 0 15px;
    color: #666;
    font-size: 20px;
}

.search-wrapper input {
    flex: 1;
    padding: 15px 0;
    border: none;
    outline: none;
    font-size: 16px;
    background: transparent;
}

.search-submit {
    background: #ECF86E;
    color: #000;
    border: none;
    padding: 12px 30px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 5px;
}

.search-submit:hover {
    background: #d4e05c;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(236, 248, 110, 0.4);
}

.search-count {
    color: rgba(255, 255, 255, 0.8);
    font-size: 18px;
}

/* Фильтры поиска */
.search-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.filter-tabs {
    display: flex;
    gap: 10px;
    background: white;
    padding: 5px;
    border-radius: 50px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.filter-tab {
    padding: 10px 25px;
    border: none;
    border-radius: 50px;
    background: transparent;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    font-weight: 500;
}

.filter-tab:hover {
    background: #f0f0f0;
}

.filter-tab.active {
    background: #000;
    color: #ECF86E;
}

.sort-options {
    display: flex;
    align-items: center;
    gap: 10px;
}

.sort-options label {
    color: #666;
    font-size: 14px;
}

.sort-select {
    padding: 10px 20px;
    border: 1px solid #e0e0e0;
    border-radius: 50px;
    outline: none;
    font-size: 14px;
    cursor: pointer;
    background: white;
}

/* Карточки событий (как на главной) */
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
    filter: brightness(0.7);
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

/* Бейджи сверху */
.event-badges-top {
    position: absolute;
    top: 15px;
    left: 15px;
    right: 15px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    z-index: 5;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    backdrop-filter: blur(4px);
}


.badge.hot {
    background: #ff4757;
    color: white;
}

.badge.festival {
    background: rgba(236, 248, 110, 0.9);
    color: #000;
}

/* Контент */
.event-content {
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

.event-title {
    font-size: 24px;
    font-weight: 700;
    color: white;
    margin: 0;
    line-height: 1.2;
}

.event-title a {
    color: white;
    text-decoration: none;
}

.event-datetime {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9);
}

.separator {
    color: rgba(255, 255, 255, 0.6);
    font-weight: bold;
}

/* Описание совпадения */
.description-match {
    background: rgba(236, 248, 110, 0.2);
    backdrop-filter: blur(4px);
    border-left: 3px solid #ECF86E;
    padding: 8px 12px;
    margin: 5px 0;
    border-radius: 8px;
    font-size: 13px;
    color: white;
    display: flex;
    gap: 8px;
    align-items: flex-start;
}

.match-icon {
    font-size: 14px;
    opacity: 0.8;
}

/* Цена и кнопка */
.price-buy {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.price-section {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.event-price {
    font-size: 13px;
    font-weight: 700;
    color: #212121ff !important;
    padding: 6px 14px;
    border-radius: 30px;
    background-color: rgba(240, 240, 240, 0.37) !important;
    backdrop-filter: blur(20px);
}

.match-indicator {
    font-size: 11px;
}

.match-badge {
    padding: 3px 8px;
    border-radius: 20px;
    font-weight: 600;
}

.match-badge.exact {
    background: #28a745;
    color: white;
}

.match-badge.partial {
    background: rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(4px);
    color: white;
}

.event-button {
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

.event-button:hover {
    background: #333;
    color: #ECF86E;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(35,35,35,0.4);
}

/* Подсветка совпадений */
mark {
    background: #ECF86E;
    color: #000;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: 600;
}

/* Пустой результат */
.no-results-found {
    max-width: 600px;
    margin: 60px auto;
    padding: 50px 20px;
    background: white;
    border-radius: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    text-align: center;
}

.no-results-found svg {
    width: 64px;
    height: 64px;
    color: #ccc;
    margin-bottom: 20px;
}

.no-results-found h2 {
    color: #333;
    font-size: 24px;
    margin-bottom: 10px;
}

.no-results-found p {
    color: #666;
    margin-bottom: 30px;
}

.search-suggestions {
    margin: 30px 0;
}

.search-suggestions h4 {
    color: #333;
    margin-bottom: 15px;
    font-size: 18px;
}

.suggestions-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
}

.suggestion-tag {
    display: inline-block;
    padding: 10px 25px;
    background: #f0f0f0;
    color: #666;
    text-decoration: none;
    border-radius: 50px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.suggestion-tag:hover {
    background: #000;
    color: #ECF86E;
    transform: translateY(-2px);
}

.btn-home {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 30px;
    background: #000;
    color: #ECF86E;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-home:hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Адаптивность */
@media (max-width: 768px) {
    .hero-title {
        font-size: 32px;
    }
    
    .search-wrapper {
        flex-direction: column;
        background: transparent;
        padding: 0;
    }
    
    .search-wrapper input {
        width: 100%;
        border-radius: 50px;
        margin-bottom: 10px;
        padding: 15px 20px;
        background: white;
    }
    
    .search-submit {
        width: 100%;
        margin: 0;
    }
    
    .search-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-tabs {
        overflow-x: auto;
        padding: 5px;
        border-radius: 50px;
    }
    
    .filter-tab {
        white-space: nowrap;
    }
    
    .events-grid {
        gap: 20px;
    }
    
    .event-title {
        font-size: 20px;
    }
    
    .event-content {
        padding: 20px 15px;
    }
    
    .event-blur {
        height: 40%;
    }
    
    .event-price {
        font-size: 16px;
    }
    
    .event-button {
        padding: 10px;
        font-size: 11px;
        width: 50%;
    }
}
</style>