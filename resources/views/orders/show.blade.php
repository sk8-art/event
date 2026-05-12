@extends('layouts.app')

@section('title', 'Заказ #' . $order->order_number)

@section('content')
<div class="order-page">
    <!-- Хлебные крошки -->
    <div class="breadcrumbs">
        <a href="{{ route('home') }}">Главная</a>
        <span class="separator">›</span>
        <a href="{{ route('profile.orders') }}">Мои заказы</a>
        <span class="separator">›</span>
        <span class="current">Заказ #{{ $order->order_number }}</span>
    </div>

    <!-- Шапка заказа -->
    <div class="order-header">
        <div>
            <h1 class="order-number">Заказ #{{ $order->order_number }}</h1>
            <p class="order-date">от {{ $order->created_at->format('d.m.Y H:i') }}</p>
        </div>
        <div class="status-badge status-{{ $order->status_color }}">
            {{ $order->status_name }}
        </div>
    </div>

    <!-- Две колонки -->
    <div class="two-columns">
        <!-- Левая колонка -->
        <div class="left-column">
            <!-- Шаг 1: Форма данных пользователя (показывается по умолчанию) -->
            <div class="step step-1" id="step1">
                <div class="step-header">
                    <span class="step-number">1</span>
                    <h2 class="step-title">Данные покупателя</h2>
                </div>
                
                <form id="userDataForm" class="step-form">
                    <div class="form-group">
                        <label for="name">Имя <span class="required">*</span></label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ auth()->user()->name ?? '' }}"
                               placeholder="Введите ваше имя"
                               class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ auth()->user()->email ?? '' }}"
                               placeholder="your@email.com"
                               class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="phone">Телефон <span class="required"></span></label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               placeholder="+7 (999) 999-99-99"
                               class="form-input">
                    </div>

                    <div class="form-note">
                        <svg width="25" height="25" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#856404"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>warning</title> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="add" fill="#666" transform="translate(32.000000, 42.666667)"> <path d="M246.312928,5.62892705 C252.927596,9.40873724 258.409564,14.8907053 262.189374,21.5053731 L444.667042,340.84129 C456.358134,361.300701 449.250007,387.363834 428.790595,399.054926 C422.34376,402.738832 415.04715,404.676552 407.622001,404.676552 L42.6666667,404.676552 C19.1025173,404.676552 7.10542736e-15,385.574034 7.10542736e-15,362.009885 C7.10542736e-15,354.584736 1.93772021,347.288125 5.62162594,340.84129 L188.099293,21.5053731 C199.790385,1.04596203 225.853517,-6.06216498 246.312928,5.62892705 Z M225.144334,42.6739678 L42.6666667,362.009885 L407.622001,362.009885 L225.144334,42.6739678 Z M224,272 C239.238095,272 250.666667,283.264 250.666667,298.624 C250.666667,313.984 239.238095,325.248 224,325.248 C208.415584,325.248 197.333333,313.984 197.333333,298.282667 C197.333333,283.264 208.761905,272 224,272 Z M245.333333,106.666667 L245.333333,234.666667 L202.666667,234.666667 L202.666667,106.666667 L245.333333,106.666667 Z" id="Combined-Shape"> </path> </g> </g> </g></svg><span class="note-text">Эти данные будут использованы для оформления билетов</span>
                    </div>

                    <button type="button" class="btn-next" onclick="goToStep2()">
                        Продолжить
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Шаг 2: Информация о заказе (скрыта изначально) -->
            <div class="step step-2" id="step2" style="display: none;">
                <div class="step-header">
                    <span class="step-number">2</span>
                    <h2 class="step-title">Детали заказа</h2>
                </div>

                <div class="event-details-card">
                    <div class="event-block">
                        <div class="event-image">
                            <img src="{{ asset('storage/' . $order->event->image) }}" 
                                 alt="{{ $order->event->title }}"
                                 onerror="this.src='{{ asset('images/event-placeholder.jpg') }}'">
                        </div>
                        <div class="event-info">
                            <h3>{{ $order->event->title }}</h3>
                            <p class="event-datetime">
                                {{ $order->event->formatted_date }} в {{ $order->event->formatted_time }}
                            </p>
                            <p class="event-location">
                                {{ $order->event->location }}{{ $order->event->address ? ', ' . $order->event->address : '' }}
                            </p>
                        </div>
                    </div>

                    <div class="order-details-section">
                        <h3 class="section-subtitle">Детали заказа</h3>
                        
                        @if($order->ticketType)
                        <div class="ticket-type-info">
                            <span class="ticket-type-name">{{ $order->ticketType->name }}</span>
                            <span class="ticket-type-price">{{ $order->formatted_unit_price }} × {{ $order->quantity }}</span>
                        </div>
                        @endif

                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Сумма</span>
                                <span>{{ $order->formatted_total_price }}</span>
                            </div>
                            <div class="summary-row total">
                                <span>Итого</span>
                                <span class="total-price">{{ $order->formatted_total_price }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button class="btn-back" onclick="goToStep1()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"/>
                                <polyline points="12 19 5 12 12 5"/>
                            </svg>
                            Назад
                        </button>
                        
                        @if($order->canBePaid())
                        <a href="{{ route('orders.payment', $order) }}" class="btn-pay">
                            Оплатить {{ $order->formatted_total_price }}
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Правая колонка (всегда видна) -->
        <div class="right-column">
            <!-- QR код -->
            <div class="card card-small">
                <h3 class="card-small-title">QR-код заказа</h3>
                <div class="qr-block">
                    @if($order->status === 'paid' || $order->status === 'confirmed')
                        <div class="qr-code">
                            {!! QrCode::size(150)->generate($order->order_number) !!}
                        </div>
                    @else
                        <div class="qr-placeholder">
                            <p class="qr-placeholder-text">Будет доступен после оплаты</p>
                        </div>
                    @endif
                </div>
                <p class="qr-hint">Покажите этот код при входе</p>
            </div>

            <!-- Таймер -->
            @if($order->status === 'pending')
            <div class="card card-small timer-card">
                <div class="timer-icon"><svg  width="44" fill="#000000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M20,3a1,1,0,0,0,0-2H4A1,1,0,0,0,4,3H5.049c.146,1.836.743,5.75,3.194,8-2.585,2.511-3.111,7.734-3.216,10H4a1,1,0,0,0,0,2H20a1,1,0,0,0,0-2H18.973c-.105-2.264-.631-7.487-3.216-10,2.451-2.252,3.048-6.166,3.194-8Zm-6.42,7.126a1,1,0,0,0,.035,1.767c2.437,1.228,3.2,6.311,3.355,9.107H7.03c.151-2.8.918-7.879,3.355-9.107a1,1,0,0,0,.035-1.767C7.881,8.717,7.227,4.844,7.058,3h9.884C16.773,4.844,16.119,8.717,13.58,10.126ZM12,13s3,2.4,3,3.6V20H9V16.6C9,15.4,12,13,12,13Z"></path></g></svg></div>
                <div class="timer-content">
                    <h4>Ожидает оплаты</h4>
                    <div class="timer-display">
                        <span class="timer-countdown-large" id="timerCountdown">15:00</span>
                    </div>
                    <p class="timer-note">Автоматическая отмена через</p>
                </div>
            </div>
            @endif

            <!-- Действия -->
            <div class="card card-small">
                <h3 class="card-small-title">Действия</h3>
                <div class="actions">
                    @if($order->status === 'paid' || $order->status === 'confirmed')
                    <!-- Кнопка возврата для оплаченных заказов -->
                    <form action="{{ route('orders.refund', $order) }}" method="POST" class="action-form">
                        @csrf
                        <button type="submit" class="btn btn-refund btn-block" onclick="return confirm('Вы уверены, что хотите вернуть билеты? Деньги будут возвращены на карту.')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Refund-back SVG Icon</title><path fill="currentColor" d="m4 8l-.707.707L2.586 8l.707-.707zm5 12a1 1 0 1 1 0-2zm-.707-6.293l-5-5l1.414-1.414l5 5zm-5-6.414l5-5l1.414 1.414l-5 5zM4 7h10.5v2H4zm10.5 13H9v-2h5.5zm6.5-6.5a6.5 6.5 0 0 1-6.5 6.5v-2a4.5 4.5 0 0 0 4.5-4.5zM14.5 7a6.5 6.5 0 0 1 6.5 6.5h-2A4.5 4.5 0 0 0 14.5 9z"/></svg>
                            Вернуть билеты
                        </button>
                    </form>
                    @endif

                    @if($order->canBeCancelled())
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="action-form">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Вы уверены, что хотите отменить заказ?')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16"><title>Cross SVG Icon</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m11.25 4.75l-6.5 6.5m0-6.5l6.5 6.5"/></svg> 
                            Отменить заказ
                        </button>
                    </form>
                    @endif

                    <button class="btn btn-secondary btn-block" onclick="window.print()">
                        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 18H6.2C5.0799 18 4.51984 18 4.09202 17.782C3.71569 17.5903 3.40973 17.2843 3.21799 16.908C3 16.4802 3 15.9201 3 14.8V10.2C3 9.0799 3 8.51984 3.21799 8.09202C3.40973 7.71569 3.71569 7.40973 4.09202 7.21799C4.51984 7 5.0799 7 6.2 7H7M17 18H17.8C18.9201 18 19.4802 18 19.908 17.782C20.2843 17.5903 20.5903 17.2843 20.782 16.908C21 16.4802 21 15.9201 21 14.8V10.2C21 9.07989 21 8.51984 20.782 8.09202C20.5903 7.71569 20.2843 7.40973 19.908 7.21799C19.4802 7 18.9201 7 17.8 7H17M7 11H7.01M17 7V5.4V4.6C17 4.03995 17 3.75992 16.891 3.54601C16.7951 3.35785 16.6422 3.20487 16.454 3.10899C16.2401 3 15.9601 3 15.4 3H8.6C8.03995 3 7.75992 3 7.54601 3.10899C7.35785 3.20487 7.20487 3.35785 7.10899 3.54601C7 3.75992 7 4.03995 7 4.6V5.4V7H7M17 7H7M8.6 21H15.4C15.9601 21 16.2401 21 16.454 20.891C16.6422 20.7951 16.7951 20.6422 16.891 20.454C17 20.2401 17 19.9601 17 19.4V16.6C17 16.0399 17 15.7599 16.891 15.546C16.7951 15.3578 16.6422 15.2049 16.454 15.109C16.2401 15 15.9601 15 15.4 15H8.6C8.03995 15 7.75992 15 7.54601 15.109C7.35785 15.2049 7.20487 15.3578 7.10899 15.546C7 15.7599 7 16.0399 7 16.6V19.4C7 19.9601 7 20.2401 7.10899 20.454C7.20487 20.6422 7.35785 20.7951 7.54601 20.891C7.75992 21 8.03995 21 8.6 21Z" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg> 
                        Распечатать
                    </button>

                    <a href="{{ route('profile.orders') }}" class="btn btn-outline btn-block">
                        Вернуться на страницу заказов
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let timerInterval;

document.addEventListener('DOMContentLoaded', function() {
    const timerElement = document.getElementById('timerCountdown');
    if (timerElement) {
        const created = {{ $order->created_at->timestamp }} * 1000;
        
        function updateTimer() {
            const now = Date.now();
            const elapsed = Math.floor((now - created) / 1000);
            const remaining = 900 - elapsed;
            
            if (remaining <= 0) {
                clearInterval(timerInterval);
                window.location.href = '{{ route("profile.orders") }}?expired={{ $order->id }}';
                return;
            }
            
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
        
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    }
});

function goToStep2() {
    const form = document.getElementById('userDataForm');
    const inputs = form.querySelectorAll('input[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
        } else {
            input.classList.remove('error');
        }
    });
    
    if (!isValid) {
        alert('Пожалуйста, заполните все обязательные поля');
        return;
    }
    
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
}

function goToStep1() {
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
}
</script>

@push('styles')
<style>
.order-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Хлебные крошки */
.breadcrumbs {
    margin-bottom: 20px;
    color: #999;
}

.breadcrumbs a {
    color: #999;
    text-decoration: none;
}

.breadcrumbs a:hover {
    color: #000;
}

.breadcrumbs .separator {
    margin: 0 5px;
}

.breadcrumbs .current {
    color: #333;
}

/* Шапка заказа */
.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
}

.order-number {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin: 0 0 5px;
}

.order-date {
    color: #666;
    font-size: 14px;
    margin: 0;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 500;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-paid {
    background: #d4edda;
    color: #155724;
}

.status-confirmed {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.status-refunded {
    background: #e2e3e5;
    color: #383d41;
}

/* Две колонки */
.two-columns {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
}

/* Левая колонка */
.left-column {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Шаги */
.step {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 30px;
}

.step-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

.step-number {
    width: 40px;
    height: 40px;
    background: #000;
    color: #ECF86E;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 600;
}

.step-title {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.step-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Форма */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.required {
    color: #dc3545;
}

.form-input {
    padding: 12px 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #000;
}

.form-input.error {
    border-color: #dc3545;
}

.form-note {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    color: #666;
    font-size: 13px;
}

/* Кнопки */
.btn-next, .btn-pay {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 25px;
    background: #000;
    color: #ECF86E;
    border: none;
    border-radius: 30px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-next:hover, .btn-pay:hover {
    background: #333;
    transform: translateY(-2px);
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    background: none;
    border: 1px solid #dee2e6;
    border-radius: 30px;
    color: #666;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-back:hover {
    background: #f8f9fa;
}

.step-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
}

/* Детали заказа */
.event-block {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
}

.event-image {
    width: 100px;
    height: 100px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.event-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.event-info h3 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0 0 10px;
}

.event-datetime,
.event-location {
    color: #666;
    font-size: 14px;
    margin: 5px 0;
}

.order-details-section {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 20px;
}

.section-subtitle {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0 0 15px;
}

.ticket-type-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    color: #666;
}

.ticket-type-name {
    font-weight: 500;
    color: #333;
}

.order-summary {
    border-top: 1px solid #dee2e6;
    padding-top: 15px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    color: #666;
}

.summary-row.total {
    font-weight: 600;
    color: #333;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #dee2e6;
}

.total-price {
    color: #000;
}

/* Карточки справа */
.card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.card-small-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0 0 15px;
}

/* QR код */
.qr-block {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.qr-placeholder {
    width: 150px;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #999;
    font-size: 13px;
}

.qr-hint {
    text-align: center;
    color: #999;
    font-size: 12px;
    margin-top: 10px;
}

/* Таймер */
.timer-card {
    display: flex;
    align-items: center;
    gap: 15px;
}

.timer-icon {
    font-size: 32px;
}

.timer-content h4 {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0 0 5px;
}

.timer-display {
    margin-bottom: 5px;
}

.timer-countdown-large {
    font-size: 24px;
    font-weight: 700;
    color: #dc3545;
}

.timer-note {
    color: #999;
    font-size: 12px;
    margin: 0;
}

/* Действия */
.actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn {
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-block {
    width: 100%;
}

.btn-danger {
    background: #666;
    color: #ffffffff;
}

.btn-danger:hover {
    background: #444;
}

.btn-refund {
    background: #000;
    color: #ECF86E;
}

.btn-refund:hover {
    background: #333;
    transform: translateY(-2px);
}

.btn-secondary {
    display: flex;
    background: #f8f9fa;
    color: #292929;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-secondary:hover {
    background: #e9ecef;
}

.btn-outline {
    background: transparent;
    border: 1px solid #dee2e6;
    color: #666;
}

.btn-outline:hover {
    background: #f8f9fa;
}

/* Адаптивность */
@media (max-width: 768px) {
    .two-columns {
        grid-template-columns: 1fr;
    }
    
    .event-block {
        flex-direction: column;
    }
    
    .event-image {
        width: 100%;
        height: 180px;
    }
    
    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .step-actions {
        flex-direction: column;
        gap: 15px;
    }
}
</style>
@endpush
@endsection