<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="{{ asset('css/style_register.css') }}">
</head>
<body>
    <img class="bg" src="{{ asset('images/fon.jpg') }}" alt="#">
    <div class="register-container">
        <div class="register-box">
            <h2>Создать аккаунт</h2>
            <p class="subtitle">Присоединяйтесь к нам!</p>
            
            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="register-form">
                @csrf
                
                <div class="input-group">
                    <label for="name">Имя</label>
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               placeholder="Иван Петров" 
                               value="{{ old('name') }}" 
                               required 
                               autofocus>
                    </div>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <span class="input-icon">📧</span>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               placeholder="your@email.com" 
                               value="{{ old('email') }}" 
                               required>
                    </div>
                </div>

                <div class="input-row">
                    <div class="input-group">
                        <label for="password">Пароль</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   placeholder="Минимум 8 символов" 
                                   required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password_confirmation">Подтверждение</label>
                        <div class="input-wrapper">
                            <span class="input-icon">✓</span>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   placeholder="Повторите пароль" 
                                   required>
                        </div>
                    </div>
                </div>

                <div class="password-requirements">
                    <p>Пароль должен содержать:</p>
                    <ul>
                        <li class="{{ old('password') && strlen(old('password')) >= 8 ? 'valid' : '' }}">
                            <span class="req-icon">✓</span> Минимум 8 символов
                        </li>
                        <li class="{{ old('password') && preg_match('/[A-Z]/', old('password')) ? 'valid' : '' }}">
                            <span class="req-icon">✓</span> Хотя бы одну заглавную букву
                        </li>
                        <li class="{{ old('password') && preg_match('/[0-9]/', old('password')) ? 'valid' : '' }}">
                            <span class="req-icon">✓</span> Хотя бы одну цифру
                        </li>
                    </ul>
                </div>

                <div class="terms">
                    <label class="checkbox">
                        <input type="checkbox" name="terms" required>
                        <span>Я принимаю <a href="#">условия использования</a> и <a href="#">политику конфиденциальности</a></span>
                    </label>
                </div>

                <button type="submit" class="btn-register">
                    <span>Зарегистрироваться</span>
                </button>

                <div class="login-link">
                    Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>