<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Оплата заказа #{{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .payment-page {
            max-width: 500px;
            width: 100%;
        }

        .payment-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        /* Хлебные крошки */
        .breadcrumbs {
            padding: 20px 30px 0;
            color: #999;
            font-size: 13px;
        }

        .breadcrumbs a {
            color: #999;
            text-decoration: none;
        }

        .breadcrumbs a:hover {
            color: #000;
        }

        .breadcrumbs .separator {
            margin: 0 8px;
        }

        .breadcrumbs .current {
            color: #333;
        }

        /* Прогресс оплаты */
        .payment-progress {
            display: flex;
            align-items: center;
            padding: 30px 30px 20px;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .step-number {
            width: 32px;
            height: 32px;
            background: #f0f0f0;
            color: #999;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .progress-step.completed .step-number {
            background: #000;
            color: #ECF86E;
        }

        .progress-step.active .step-number {
            background: #000;
            color: #ECF86E;
        }

        .step-label {
            font-size: 12px;
            color: #666;
        }

        .progress-line {
            flex: 1;
            height: 2px;
            background: #f0f0f0;
            margin: 0 5px;
        }

        /* Заголовок */
        .payment-header {
            padding: 0 30px 20px;
        }

        .payment-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 0 0 5px;
        }

        .payment-header .order-number {
            color: #666;
            font-size: 14px;
        }

        

        /* Форма оплаты */
        .payment-form-section {
            padding: 0 30px 30px;
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .payment-method {
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.2s;
            opacity: 0.6;
        }

        .payment-method.unavailable {
            cursor: not-allowed;
            background: #f8f9fa;
            filter: grayscale(1);
        }

        .payment-method.available {
            opacity: 1;
            cursor: pointer;
        }

        .payment-method.available:hover {
            background: #f8f9fa;
            border-color: #000;
        }

        .payment-method input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .method-icon {
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: all 0.2s;
        }

        .method-info {
            flex: 1;
        }

        .method-name {
            display: block;
            font-weight: 500;
            color: #333;
            margin-bottom: 3px;
        }

        .method-desc {
            font-size: 12px;
            color: #999;
        }

        .unavailable-badge {
            font-size: 11px;
            color: #999;
            background: #f0f0f0;
            padding: 3px 8px;
            border-radius: 12px;
        }

        .method-check {
            color: #000;
            font-size: 18px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .payment-method.available input[type="radio"]:checked ~ .method-check {
            opacity: 1;
        }

        /* Детали наличных */
        .cash-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .cash-details p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .cash-warning {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            text-align: center;
        }

        /* Кнопки */
        .payment-actions {
            display: flex;
            gap: 15px;
            margin: 20px 0;
        }

        .btn-pay,
        .btn-back {
            padding: 12px 25px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-pay {
            flex: 2;
            background: #000;
            color: #ECF86E;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-pay:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .btn-pay.disabled {
            background: #ccc;
            color: #666;
            cursor: not-allowed;
            pointer-events: none;
        }

        .btn-back {
            flex: 1;
            background: transparent;
            border: 1px solid #dee2e6;
            color: #666;
        }

        .btn-back:hover {
            background: #f8f9fa;
        }

        .btn-price {
            font-weight: 600;
            color: #ECF86E;
        }

        /* Безопасность */
        .payment-security {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
        }

        .security-icon {
            font-size: 20px;
        }

        .security-text {
            color: #666;
            font-size: 13px;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .payment-progress {
                padding: 20px 15px;
            }
            
            .payment-header {
                padding: 0 15px 15px;
            }
            
            .payment-form-section {
                padding: 0 15px 20px;
            }
            
            .step-label {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="payment-page">
        <div class="payment-container">
            <!-- Хлебные крошки -->
            <div class="breadcrumbs">
                <a href="{{ route('home') }}">Главная</a>
                <span class="separator">›</span>
                <a href="{{ route('profile.orders') }}">Мои заказы</a>
                <span class="separator">›</span>
                <a href="{{ route('orders.show', $order) }}">Заказ #{{ $order->order_number }}</a>
                <span class="separator">›</span>
                <span class="current">Оплата</span>
            </div>

            <!-- Прогресс оплаты -->
            <div class="payment-progress">
                <div class="progress-step completed">
                    <span class="step-number">1</span>
                    <span class="step-label">Детали</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step active">
                    <span class="step-number">2</span>
                    <span class="step-label">Оплата</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <span class="step-number">3</span>
                    <span class="step-label">Готово</span>
                </div>
            </div>

            <div class="payment-header">
                <h1>Оплата заказа</h1>
                <p class="order-number">Заказ #{{ $order->order_number }}</p>
            </div>

            

            <!-- Форма оплаты -->
            <div class="payment-form-section">
                <form action="{{ route('orders.payment.process', $order) }}" method="POST" id="paymentForm">
                    @csrf
                    
                    <div class="payment-methods">
                        <!-- Карта - недоступна -->
                        <div class="payment-method unavailable">
                            <span class="method-icon"><svg width="24" height="24" fill="#000000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M4,21H20a3,3,0,0,0,3-3V6a3,3,0,0,0-3-3H4A3,3,0,0,0,1,6V18A3,3,0,0,0,4,21ZM3,6A1,1,0,0,1,4,5H20a1,1,0,0,1,1,1V18a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1ZM5,16a1,1,0,0,1,1-1H9a1,1,0,0,1,0,2H6A1,1,0,0,1,5,16Zm0-3a1,1,0,0,1,1-1h6a1,1,0,0,1,0,2H6A1,1,0,0,1,5,13Z"></path></g></svg></span>
                            <div class="method-info">
                                <span class="method-name">Банковская карта</span>
                                <span class="method-desc">Временно недоступно</span>
                            </div>
                            <span class="unavailable-badge">Скоро</span>
                        </div>
                        
                        
                        
                        <!-- Наличные - доступны -->
                        <label class="payment-method available">
                            <input type="radio" name="payment_method" value="cash" checked>
                            <span class="method-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 32 32"><title>Money SVG Icon</title><path fill="currentColor" d="M2 22h28v2H2zm0 4h28v2H2zm22-16a2 2 0 1 0 2 2a2 2 0 0 0-2-2m-8 6a4 4 0 1 1 4-4a4.005 4.005 0 0 1-4 4m0-6a2 2 0 1 0 2 2a2.002 2.002 0 0 0-2-2m-8 0a2 2 0 1 0 2 2a2 2 0 0 0-2-2"/><path fill="currentColor" d="M28 20H4a2.005 2.005 0 0 1-2-2V6a2.005 2.005 0 0 1 2-2h24a2.005 2.005 0 0 1 2 2v12a2.003 2.003 0 0 1-2 2m0-14H4v12h24Z"/></svg></span>
                            <div class="method-info">
                                <span class="method-name">Наличные</span>
                                <span class="method-desc">При получении в кассе</span>
                            </div>
                            <span class="method-check">✓</span>
                        </label>
                    </div>

                    <!-- Наличные форма -->
                    <div class="cash-details">
                        <p>Вы сможете оплатить заказ наличными в кассе перед мероприятием.</p>
                        <div class="cash-warning">
                            <svg width="15" height="15" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#856404"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>warning</title> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="add" fill="#856404" transform="translate(32.000000, 42.666667)"> <path d="M246.312928,5.62892705 C252.927596,9.40873724 258.409564,14.8907053 262.189374,21.5053731 L444.667042,340.84129 C456.358134,361.300701 449.250007,387.363834 428.790595,399.054926 C422.34376,402.738832 415.04715,404.676552 407.622001,404.676552 L42.6666667,404.676552 C19.1025173,404.676552 7.10542736e-15,385.574034 7.10542736e-15,362.009885 C7.10542736e-15,354.584736 1.93772021,347.288125 5.62162594,340.84129 L188.099293,21.5053731 C199.790385,1.04596203 225.853517,-6.06216498 246.312928,5.62892705 Z M225.144334,42.6739678 L42.6666667,362.009885 L407.622001,362.009885 L225.144334,42.6739678 Z M224,272 C239.238095,272 250.666667,283.264 250.666667,298.624 C250.666667,313.984 239.238095,325.248 224,325.248 C208.415584,325.248 197.333333,313.984 197.333333,298.282667 C197.333333,283.264 208.761905,272 224,272 Z M245.333333,106.666667 L245.333333,234.666667 L202.666667,234.666667 L202.666667,106.666667 L245.333333,106.666667 Z" id="Combined-Shape"> </path> </g> </g> </g></svg> Не забудьте взять с собой паспорт для получения билетов
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert-danger">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="payment-actions">
                        <button type="submit" class="btn-pay" id="payButton">
                            Оплатить наличными
                        </button>
                        
                        <a href="{{ route('orders.show', $order) }}" class="btn-back">
                            Отмена
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        // Таймер
        const timerElement = document.getElementById('paymentTimer');
        if (timerElement) {
            let timeLeft = 15 * 60;
            const interval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(interval);
                    timerElement.textContent = 'Время вышло';
                    document.getElementById('payButton').disabled = true;
                    document.getElementById('payButton').classList.add('disabled');
                    return;
                }
                
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                timeLeft--;
            }, 1000);
        }

        // Валидация формы
        document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
            const cashRadio = document.querySelector('input[name="payment_method"][value="cash"]');
            if (!cashRadio.checked) {
                e.preventDefault();
                alert('Выберите способ оплаты');
            }
        });
    </script>
</body>
</html>