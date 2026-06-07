@extends('layouts.app')

@section('title', 'Мои заказы')

@section('content')
<div class="orders-page">
    <div class="page-header">
        <h1 class="page-title">Мои заказы</h1>
    </div>
    

    @php
        $cancelledEvents = collect($orders ?? [])->filter(function($order) {
            return $order->status === 'cancelled' && 
                str_contains($order->notes ?? '', 'Мероприятие отменено');
        });
    @endphp

    @if($cancelledEvents->count() > 0)
        <div class="cancelled-notification" id="cancelledNotification">
            <div class="notification-icon"><svg width="35" height="35" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#856404"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>warning</title> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="add" fill="#856404" transform="translate(32.000000, 42.666667)"> <path d="M246.312928,5.62892705 C252.927596,9.40873724 258.409564,14.8907053 262.189374,21.5053731 L444.667042,340.84129 C456.358134,361.300701 449.250007,387.363834 428.790595,399.054926 C422.34376,402.738832 415.04715,404.676552 407.622001,404.676552 L42.6666667,404.676552 C19.1025173,404.676552 7.10542736e-15,385.574034 7.10542736e-15,362.009885 C7.10542736e-15,354.584736 1.93772021,347.288125 5.62162594,340.84129 L188.099293,21.5053731 C199.790385,1.04596203 225.853517,-6.06216498 246.312928,5.62892705 Z M225.144334,42.6739678 L42.6666667,362.009885 L407.622001,362.009885 L225.144334,42.6739678 Z M224,272 C239.238095,272 250.666667,283.264 250.666667,298.624 C250.666667,313.984 239.238095,325.248 224,325.248 C208.415584,325.248 197.333333,313.984 197.333333,298.282667 C197.333333,283.264 208.761905,272 224,272 Z M245.333333,106.666667 L245.333333,234.666667 L202.666667,234.666667 L202.666667,106.666667 L245.333333,106.666667 Z" id="Combined-Shape"> </path> </g> </g> </g></svg></div>
            <div class="notification-content">
                <h4>Внимание! Некоторые мероприятия были отменены</h4>
                <p>Следующие мероприятия были отменены организаторами. <br> Ваши заказы автоматически отменены, билеты возвращены.</p>
                <ul class="cancelled-list">
                    @foreach($cancelledEvents as $order)
                        <li>
                            <strong>{{ $order->event->title ?? 'Мероприятие' }}</strong>
                            @if($order->notes)
                            @endif
                        </li>
                    @endforeach
                </ul>
                <button class="btn-hide-notification" onclick="hideNotificationForever()">
                    Понятно, больше не показывать
                </button>
            </div>
        </div>
    @endif

    <div class="orders-grid">
        @php
            // Фильтруем заказы, исключая отмененные
            $activeOrders = collect($orders ?? [])->filter(function($order) {
                return !in_array($order->status, ['cancelled', 'refunded']);
            });
        @endphp

        @forelse($activeOrders as $order)
            <div class="order-card" data-order-id="{{ $order->id }}" onclick="window.location='{{ route('orders.show', $order) }}'">
                <!-- Изображение сверху -->
                <div class="order-image">
                    <img src="{{ $order->event && $order->event->image ? asset('storage/' . $order->event->image) : '' }}" alt="{{ $order->event->title ?? 'Мероприятие' }}">
                    
                    <!-- Статус заказа на изображении -->
                    <span class="order-status status-{{ $order->status_color }}">{{ $order->status_name }}</span>
                    
                    <!-- Кнопка отмены на изображении -->
                    @if($order->canBeCancelled())
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" class="cancel-form" onclick="event.stopPropagation()">
                            @csrf
                            <button type="submit" class="cancel-button" onclick="return confirm('Вы уверены, что хотите отменить заказ?')" title="Отменить заказ">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                                Отменить
                            </button>
                        </form>
                    @endif

                    <!-- Таймер для неоплаченных заказов -->
                    @if($order->status === 'pending')
                        <div class="order-timer" data-created="{{ $order->created_at->timestamp }}" data-order-id="{{ $order->id }}">
                            <span class="timer-icon"><svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="#ffffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12 6V12" stroke="#ffffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M16.24 16.24L12 12" stroke="#ffffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg></span>
                            <span class="timer-text">Осталось: <span class="timer-countdown">15:00</span></span>
                        </div>
                    @endif
                </div>
                
                <!-- Контент снизу -->
                <div class="order-content">
                    <h3 class="order-event-title">{{ $order->event->title ?? 'Мероприятие не найдено' }}</h3>
                    
                    <div class="order-datetime">
                        @if($order->event)
                            <span>{{ $order->event->russian_date }}</span>
                            <span class="separator">•</span>
                            <span>{{ $order->event->formatted_time }}</span>
                        @endif
                    </div>
                    
                    <div class="order-footer">
                        <span class="order-price">{{ $order->formatted_total_price }}</span>
                        
                        <div class="order-actions">
                            @if($order->canBePaid())
                                <a href="{{ route('orders.payment', $order) }}" class="order-button pay" onclick="event.stopPropagation()">
                                    Оплатить
                                </a>
                            @else
                                <a href="{{ route('orders.show', $order) }}" class="order-button details" onclick="event.stopPropagation()">
                                    Детали
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-results">
                <div class="no-results-content">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.9536 14.9458L21 21M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h3>У вас пока нет активных заказов</h3>
                    <p>Перейдите к мероприятиям и выберите что-нибудь интересное</p>
                    <a href="{{ route('home') }}" class="btn-reset-large">
                        <span><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><title>Ticket SVG Icon</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2z"/></svg></span>
                        Посмотреть мероприятия
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
// Проверяем при загрузке страницы, нужно ли показывать уведомление
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('cancelledNotification');
    if (notification) {
        const hidden = localStorage.getItem('cancelledNotificationHidden');
        const hiddenTime = localStorage.getItem('cancelledNotificationHiddenTime');
        
        // Если пользователь скрыл уведомление меньше часа назад, не показываем
        if (hidden === 'true' && hiddenTime) {
            const now = new Date().getTime();
            const oneHour = 60 * 60 * 1000; // 1 час в миллисекундах
            
            if (now - parseInt(hiddenTime) < oneHour) {
                notification.style.display = 'none';
            } else {
                // Если прошло больше часа, показываем снова
                localStorage.removeItem('cancelledNotificationHidden');
                localStorage.removeItem('cancelledNotificationHiddenTime');
            }
        }
    }

    // Таймеры для просроченных заказов
    const timers = document.querySelectorAll('.order-timer');
    
    timers.forEach(timer => {
        const created = parseInt(timer.dataset.created) * 1000; // конвертируем в миллисекунды
        const orderId = timer.dataset.orderId;
        const countdownEl = timer.querySelector('.timer-countdown');
        const orderCard = timer.closest('.order-card');
        
        function updateTimer() {
            const now = Date.now();
            const elapsed = (now - created) / 1000;
            const remaining = 900 - elapsed;
            
            if (remaining <= 0) {
                // Время истекло
                timer.innerHTML = '<span class="timer-expired">⏰ Отмена...</span>';
                
                // Отправляем запрос на сервер для отмены заказа
                fetch(`/orders/${orderId}/auto-cancel`, {
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
                        // Плавно скрываем карточку
                        orderCard.style.transition = 'all 0.5s ease';
                        orderCard.style.opacity = '0';
                        orderCard.style.transform = 'translateY(20px)';
                        
                        setTimeout(() => {
                            orderCard.remove();
                            
                            // Проверяем, остались ли еще заказы
                            const remainingCards = document.querySelectorAll('.order-card');
                            if (remainingCards.length === 0) {
                                location.reload();
                            }
                        }, 500);
                        
                        // Показываем уведомление
                        showNotification('Заказ отменен');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    timer.innerHTML = '<span class="timer-expired">⏰ Ошибка</span>';
                });
                
                return;
            }
            
            const minutes = Math.floor(remaining / 60);
            const seconds = Math.floor(remaining % 60);
            countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
                
        updateTimer();
        setInterval(updateTimer, 1000);
    });
});

function hideNotificationForever() {
    const notification = document.getElementById('cancelledNotification');
    
    // Сохраняем в localStorage что пользователь скрыл уведомление
    localStorage.setItem('cancelledNotificationHidden', 'true');
    localStorage.setItem('cancelledNotificationHiddenTime', new Date().getTime().toString());
    
    // Плавно скрываем
    notification.style.animation = 'slideUp 0.3s ease forwards';
    
    setTimeout(() => {
        notification.remove();
    }, 300);
}

function showNotification(message) {
    // Проверяем, есть ли контейнер для уведомлений
    let container = document.getElementById('notification-container');
    
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        `;
        document.body.appendChild(container);
    }
    
    // Создаем уведомление
    const notification = document.createElement('div');
    notification.style.cssText = `
        background: #ECF86E;
        color: #000000;
        padding: 12px 24px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
        cursor: pointer;
        font-weight: 500;
    `;
    notification.textContent = message;
    
    container.appendChild(notification);
    
    // Удаляем через 3 секунды
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Добавляем анимации
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush

<style>

/* Стили для уведомлений */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}


.orders-page {
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

.section-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin-bottom: 30px;
    position: relative;
    padding-bottom: 10px;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: #ECF86E;
    border-radius: 2px;
}

/* Статистика админа */
.admin-stats {
    margin-bottom: 50px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    background: white;
    border-radius: 30px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: #f8f9fa;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-content {
    flex: 1;
}

.stat-value {
    display: block;
    font-size: 28px;
    font-weight: 700;
    color: #333;
    line-height: 1.2;
}

.stat-label {
    color: #666;
    font-size: 14px;
}

/* Сетка заказов */
.orders-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

/* Карточка заказа в стиле "Популярно" */
.order-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s;
    cursor: pointer;
    border: 1px solid #f0f0f0;
}

.order-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.order-image {
    position: relative;
    width: 100%;
    height: 160px;
    overflow: hidden;
}

.order-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.order-card:hover .order-image img {
    transform: scale(1.05);
}

/* Статус заказа */
.order-status {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    z-index: 2;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    backdrop-filter: blur(13px);
    color: #fff;
}

.status-pending {
    background: rgba(255, 193, 7, 0.95);
    color: #000;
}

.status-paid {
    background: rgba(40, 167, 69, 0.95);
    color: white;
}

.status-confirmed {
    background: rgba(0, 123, 255, 0.95);
    color: white;
}

.status-cancelled {
    background: rgba(220, 53, 69, 0.95);
    color: white;
}

.status-refunded {
    background: rgba(108, 117, 125, 0.95);
    color: white;
}

/* Кнопка отмены */
.cancel-form {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 10;
}

.cancel-button {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 20px;
    border: none;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    color: white;
    font-size: 11px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.cancel-button:hover {
    background: #ECF86E;
    color: #000;
}

.cancel-button svg {
    width: 12px;
    height: 12px;
}

/* Таймер */
.order-timer {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
    color: #ffc107;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    display: flex;
    align-items: center;
    gap: 5px;
    z-index: 10;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.timer-icon {
    font-size: 12px;
}

.timer-countdown {
    font-weight: 700;
    color: #fff;
    background: rgba(0, 0, 0, 0.3);
    padding: 2px 4px;
    border-radius: 10px;
    margin-left: 2px;
}

.timer-expired {
    color: #dc3545;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Контент */
.order-content {
    padding: 16px 15px 18px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.order-event-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    line-height: 1.3;
    margin: 0;
}

.order-datetime {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: #666;
}

.separator {
    color: #666;
}

.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 4px;
}

.order-price {
    font-size: 18px;
    font-weight: 700;
    color: #000;
}

.order-actions {
    display: flex;
    gap: 8px;
}

.order-button {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    white-space: nowrap;
}

.order-button.pay {
    background: #ECF86E;
    color: #000;
}

.order-button.pay:hover {
    background: #d4e05c;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(236, 248, 110, 0.3);
}

.order-button.details {
    background: #f0f0f0;
    color: #333;
}

.order-button.details:hover {
    background: #e4e4e4;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Уведомление об отмене */
.cancelled-notification {
    background: #fff3cd;
    border-radius: 15px;
    padding: 20px;
    display: flex;
    gap: 15px;
    align-items: flex-start;
    animation: slideDown 0.3s ease;
    width: fit-content;
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

.notification-icon {
    font-size: 24px;
    line-height: 1;
}

.notification-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.notification-content h4 {
    color: #856404;
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}

.notification-content p {
    color: #856404;
    margin: 0;
}

.cancelled-list {
    list-style: none;
    padding: 0;
    margin: 0;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 10px;
    padding: 10px 15px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: fit-content;
}

.cancelled-list li {
    border-bottom: 1px dashed #ffeeba;
    padding-bottom: 8px;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.cancelled-list li:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.cancelled-reason {
    font-size: 13px;
    color: #856404;
    font-style: italic;
}

.btn-hide-notification {
    background: #856404;
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 30px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    align-self: flex-start;
}

.btn-hide-notification:hover {
    background: #6d5300;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(133, 100, 4, 0.3);
}

/* Пустое состояние */
.no-results {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.no-results-content {
    max-width: 400px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.no-results svg {
    width: 40px;
    height: 40px;
    color: #ffffffff;
}

.no-results h3 {
    color: #333;
    font-size: 24px;
}

.no-results p {
    color: #666;
}

.btn-reset-large {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 6px 20px !important;
    color: #ffffffff;
    text-decoration: none;
    border-radius: 35px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-reset-large:hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Адаптивность */
@media (max-width: 768px) {
    .orders-grid {
        gap: 20px;
    }
    
    .order-event-title {
        font-size: 16px;
    }
    
    .order-price {
        font-size: 16px;
    }
    
    .order-button {
        padding: 4px 10px;
        font-size: 10px;
    }
    
    .order-timer {
        font-size: 10px;
        padding: 3px 8px;
    }
}
</style>
@endsection