@extends('layouts.app')

@section('title', 'Мои мероприятия')

@section('content')
<div class="organizer-page">
    <div class="page-header">
        <div class="header-top">
            <h1>Мои мероприятия</h1>
            <a href="{{ route('organizer.create') }}" class="btn-create"><svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 12H20M12 4V20" stroke="#ECF86E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg> Создать мероприятие</a>
        </div>
    </div>


    @if(isset($events) && $events->count() > 0)
        <div class="events-list">
            @foreach($events as $event)
                <div class="event-item">
                    <div class="event-image">
                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
                        <span class="event-status status-{{ $event->status_color }}">
                            {{ $event->status_name }}
                        </span>
                    </div>
                    
                    <div class="event-details">
                        <div class="event-header">
                            <h3>{{ $event->title }}</h3>
                            <div class="event-header-actions">
                                <a href="{{ route('events.show', $event) }}" class="action-icon" target="_blank" title="Просмотр">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="2"/>
                                        <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('organizer.edit', $event) }}" class="action-icon" title="Редактировать">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('orders.export.csv', $event) }}" class="action-icon" title="Экспорт заказов">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 3v12m0 0-3-3m3 3 3-3M5 21h14"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        <div class="event-meta">
                            <div class="meta-item">
                                <span class="meta-label">Дата:</span>
                                <span class="meta-value">{{ $event->formatted_date }} в {{ $event->formatted_time }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Место:</span>
                                <span class="meta-value">{{ $event->location }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Тип:</span>
                                <span class="meta-value">{{ $event->type_name }}</span>
                            </div>
                        </div>
                        
                        <div class="event-stats">
                            <div class="stat">
                                <span class="stat-value">{{ $event->sold_tickets }}</span>
                                <span class="stat-label">продано</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">{{ $event->available_tickets }}</span>
                                <span class="stat-label">осталось</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">{{ number_format($event->revenue, 0, ',', ' ') }} ₽</span>
                                <span class="stat-label">выручка</span>
                            </div>
                        </div>
                        
                        <div class="event-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $event->fill_percentage }}%"></div>
                            </div>
                            <span class="progress-text">{{ $event->fill_percentage }}% заполнено</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination">
            {{ $events->links() }}
        </div>
    @else
        <div class="no-events">
            <p>У вас пока нет созданных мероприятий</p>
            <a href="{{ route('organizer.create') }}" class="btn-create-large">Создать первое мероприятие</a>
        </div>
    @endif
</div>

<!-- Модальное окно с заказами (оставлено на случай, если понадобится) -->
<div id="ordersModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Заказы на мероприятие</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body" id="ordersList">
            <!-- Сюда будут загружаться заказы -->
        </div>
    </div>
</div>

<script>
function showOrders(eventId) {
    // Здесь будет AJAX запрос для получения заказов
    const modal = document.getElementById('ordersModal');
    modal.style.display = 'block';
    
    // Заглушка для примера
    document.getElementById('ordersList').innerHTML = '<p>Загрузка...</p>';
}

// Закрытие модального окна
document.querySelector('.close').onclick = function() {
    document.getElementById('ordersModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('ordersModal');
    if (event.target == modal) {
        modal.style.display = 'none';
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
}

.page-header {
    margin-bottom: 40px;
}

.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-top h1 {
    font-size: 28px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.btn-create {
    background: #000;
    color: #ECF86E;
    padding: 10px 20px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-create:hover {
    background: #333;
    transform: translateY(-2px);
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    font-size: 14px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.events-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.event-item {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    display: grid;
    grid-template-columns: 150px 1fr;
    gap: 20px;
    transition: all 0.2s;
}

.event-item:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.event-image {
    position: relative;
}

.event-image img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
}

.event-status {
    position: absolute;
    top: -8px;
    right: -8px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    color: white;
}

.status-green {
    background: #ECF86E;
    color: #000;
}
.status-red {
    background: #dc3545;
}
.status-gray {
    background: #000;
}

.event-details {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.event-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.event-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.event-header-actions {
    display: flex;
    gap: 8px;
}

.action-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    transition: all 0.2s;
}

.action-icon:hover {
    background: #000;
    color: #ECF86E;
    transform: scale(1.1);
}

.event-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.meta-item {
    font-size: 14px;
    color: #666;
}

.meta-label {
    color: #999;
    margin-right: 5px;
}

.meta-value {
    color: #333;
    font-weight: 500;
}

.event-stats {
    display: flex;
    gap: 20px;
    margin: 5px 0;
    padding: 15px 0;
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
}

.stat {
    display: flex;
    align-items: baseline;
    gap: 5px;
}

.stat-value {
    font-size: 16px;
    font-weight: 600;
    color: #000;
}

.stat-label {
    font-size: 12px;
    color: #999;
}

.event-progress {
    display: flex;
    align-items: center;
    gap: 10px;
}

.progress-bar {
    flex: 1;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #000;
    transition: width 0.3s;
}

.progress-text {
    font-size: 12px;
    color: #666;
    min-width: 80px;
}

/* Пустое состояние */
.no-events {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
}

.no-events p {
    color: #666;
    margin-bottom: 20px;
}

.btn-create-large {
    display: inline-block;
    padding: 12px 30px;
    background: #000;
    color: #ECF86E;
    text-decoration: none;
    border-radius: 30px;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-create-large:hover {
    background: #333;
    transform: translateY(-2px);
}

/* Модальное окно */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.3);
    backdrop-filter: blur(4px);
}

.modal-content {
    background: white;
    margin: 10% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
}
.btn-create {
    display: flex;
    align-items: center;
    gap: 8px;
}
.modal-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.close {
    font-size: 24px;
    cursor: pointer;
    color: #999;
    transition: color 0.2s;
}

.close:hover {
    color: #000;
}

.modal-body {
    padding: 20px;
    min-height: 100px;
}

/* Пагинация */
.pagination {
    margin-top: 40px;
    text-align: center;
}

/* Адаптивность */
@media (max-width: 768px) {
    .header-top {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .event-item {
        grid-template-columns: 1fr;
    }
    
    .event-image img {
        height: 160px;
    }
    
    .event-stats {
        flex-wrap: wrap;
    }
}
</style>
@endpush