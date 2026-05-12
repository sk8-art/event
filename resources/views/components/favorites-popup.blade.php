<div class="favorites-popup-content-inner">
    @if($favorites->count() > 0)
        <div class="favorites-list">
            @foreach($favorites as $event)
                <div class="favorite-item" data-event-id="{{ $event->id }}">
                    <div class="favorite-item-image">
                        <img src="{{ asset('storage/' . $event->image) }}" 
                             alt="{{ $event->title }}"
                             onerror="this.src='{{ asset('images/event-placeholder.jpg') }}'">
                    </div>
                    <div class="favorite-item-info">
                        <h4 class="favorite-item-title">
                            <a href="{{ route('events.show', $event) }}">{{ $event->title }}</a>
                        </h4>
                        <div class="favorite-item-meta">
                            <span class="favorite-item-date">{{ $event->russian_date }}</span>
                            <span class="favorite-item-price">
                                @if($event->price > 0)
                                    {{ number_format($event->price, 0, ',', ' ') }} ₽
                                @else
                                    Бесплатно
                                @endif
                            </span>
                        </div>
                    </div>
                    <button class="favorite-item-remove" onclick="removeFromFavorites({{ $event->id }})" title="Удалить">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
        
        @if($favorites->count() >= 5)
            <div class="favorites-popup-footer">
                <a href="{{ route('favorites.list') }}" class="view-all-link">
                    Посмотреть все
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>
        @endif
    @else
        <div class="favorites-empty">
            <svg class="empty-icon" width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M24 42L6.3 24.3C3.9 21.9 3 18.7 3 15.5C3 9 8.5 3.5 15 3.5C18.5 3.5 21.9 5.2 24 8C26.1 5.2 29.5 3.5 33 3.5C39.5 3.5 45 9 45 15.5C45 18.7 44.1 21.9 41.7 24.3L24 42Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
            <p>У вас пока нет избранных мероприятий</p>
            <a href="{{ route('concerts') }}" class="browse-link">Посмотреть концерты</a>
        </div>
    @endif
</div>

<style>
.favorites-list {
    max-height: 350px;
    overflow-y: auto;
    padding: 5px;
}

.favorite-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 15px;
    border-radius: 12px;
    transition: all 0.3s ease;
    margin-bottom: 5px;
}

.favorite-item:hover {
    background: #f8f9fa;
}

.favorite-item:last-child {
    margin-bottom: 0;
}

.favorite-item-image {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.favorite-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.favorite-item:hover .favorite-item-image img {
    transform: scale(1.05);
}

.favorite-item-info {
    flex: 1;
    min-width: 0;
}

.favorite-item-title {
    margin: 0 0 6px 0;
    font-size: 15px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.favorite-item-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.2s ease;
}

.favorite-item-title a:hover {
    color: #ECF86E;
}

.favorite-item-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
    color: #666;
}

.favorite-item-date {
    display: flex;
    align-items: center;
    gap: 4px;
}

.favorite-item-price {
    font-weight: 600;
    color: #ECF86E;
    background: rgba(236, 248, 110, 0.1);
    padding: 2px 8px;
    border-radius: 20px;
}

.favorite-item-remove {
    background: none;
    border: none;
    color: #999;
    font-size: 18px;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.3s ease;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.favorite-item-remove svg {
    width: 16px;
    height: 16px;
}

.favorite-item-remove:hover {
    background: #fee2e2;
    color: #ff4757;
    transform: rotate(90deg);
}

/* Пустое состояние */
.favorites-empty {
    text-align: center;
    padding: 50px 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.favorites-empty .empty-icon {
    width: 64px;
    height: 64px;
    color: #ddd;
}

.favorites-empty p {
    color: #666;
    font-size: 15px;
    margin: 0;
}

.favorites-empty .browse-link {
    display: inline-block;
    padding: 10px 24px;
    background: #000;
    color: #ECF86E;
    text-decoration: none;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.favorites-empty .browse-link:hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Футер */
.favorites-popup-footer {
    padding: 15px 10px 5px;
    text-align: center;
    border-top: 1px solid #f0f0f0;
}

.view-all-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 30px;
    transition: all 0.3s ease;
}

.view-all-link:hover {
    background: #f8f9fa;
    gap: 12px;
}

.view-all-link svg {
    transition: transform 0.3s ease;
}

.view-all-link:hover svg {
    transform: translateX(3px);
}

/* Стилизация скроллбара */
.favorites-list::-webkit-scrollbar {
    width: 5px;
}

.favorites-list::-webkit-scrollbar-track {
    background: transparent;
}

.favorites-list::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 10px;
}

.favorites-list::-webkit-scrollbar-thumb:hover {
    background: #ccc;
}
</style>