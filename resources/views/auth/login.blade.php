<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="{{ asset('css/style_login.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            filter: blur(3px) brightness(0.7);
            transform: scale(1.1);
        }
        .back-to-site {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 100;
        }
        
        .back-to-site a {
            background: #000;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            padding: 0px 15px;
        }


        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
            animation: fadeIn 0.5s ease;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #333;
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: center;
        }

        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-group label {
            color: #555;
            font-size: 14px;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            display: flex;
            position: absolute;
            left: 15px;
            color: #666;
            font-size: 18px;
        }

        .input-wrapper input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #656565;
        }

        .input-wrapper input::placeholder {
            color: #999;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .forgot-password {
            color: #000;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: #ECF86E;
            text-decoration: underline;
        }

        .btn-login {
            background: #000;
            color: #ffffff;
            border: none;
            padding: 15px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background: #ECF86E;
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login span {
            position: relative;
            z-index: 1;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }

        .register-link {
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .register-link a {
            color: #000;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: #ECF86E;
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .back-to-site {
                top: 10px;
                left: 10px;
            }
            
            .back-to-site a {
                padding: 8px 15px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .login-box {
                padding: 30px 20px;
            }

            h2 {
                font-size: 28px;
            }

            .form-options {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
        
        .back-to-site a:hover {
            background: rgba(28, 28, 28, 0.6);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .back-to-site span {
            font-size: 18px;
        }
        
        /* Адаптивность для мобильных */
        @media (max-width: 768px) {
            .back-to-site {
                top: 10px;
                left: 10px;
            }
            
            .back-to-site a {
                padding: 8px 15px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <!-- Кнопка перехода на главную страницу -->
    <div class="back-to-site">
        <a href="{{ route('home') }}">
            <svg class="ticket-icon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 512 512">
                        <title>Ticket SVG Icon</title>
                        <style>
                            @keyframes drawTicket {
                            0% {
                                stroke-dashoffset: 2000;
                            }
                            100% {
                                stroke-dashoffset: 0;
                            }
                            }
                            
                            .ticket-path {
                            fill: none;
                            stroke: #ECF86E;    
                            stroke-width: 21;
                            stroke-dasharray: 2000;
                            stroke-dashoffset: 2000;
                            animation: drawTicket 2s ease-in-out forwards;
                            }
                        </style>
                        <path transform="rotate(45 256 256)" class="ticket-path" d="m490.18 181.4l-44.13-44.13a20 20 0 0 0-27-1a30.81 30.81 0 0 1-41.68-1.6a30.81 30.81 0 0 1-1.6-41.67a20 20 0 0 0-1-27L330.6 21.82a19.91 19.91 0 0 0-28.13 0l-70.35 70.34a39.87 39.87 0 0 0-9.57 15.5a7.71 7.71 0 0 1-4.83 4.83a39.78 39.78 0 0 0-15.5 9.58l-180.4 180.4a19.91 19.91 0 0 0 0 28.13L66 374.73a20 20 0 0 0 27 1a30.69 30.69 0 0 1 43.28 43.28a20 20 0 0 0 1 27l44.13 44.13a19.91 19.91 0 0 0 28.13 0l180.4-180.4a39.82 39.82 0 0 0 9.58-15.49a7.69 7.69 0 0 1 4.84-4.84a39.84 39.84 0 0 0 15.49-9.57l70.34-70.35a19.91 19.91 0 0 0-.01-28.09m-228.37-29.65a16 16 0 0 1-22.63 0l-11.51-11.51a16 16 0 0 1 22.63-22.62l11.51 11.5a16 16 0 0 1 0 22.63m44 44a16 16 0 0 1-22.62 0l-11-11a16 16 0 1 1 22.63-22.63l11 11a16 16 0 0 1 .01 22.66Zm44 44a16 16 0 0 1-22.63 0l-11-11a16 16 0 0 1 22.63-22.62l11 11a16 16 0 0 1 .05 22.67Zm44.43 44.54a16 16 0 0 1-22.63 0l-11.44-11.5a16 16 0 1 1 22.68-22.57l11.45 11.49a16 16 0 0 1-.01 22.63Z"/>
                        </svg>
                    <span class="logo-text">Twilight</span>
        </a>
    </div>
    
    <img class="bg" src="{{ asset('images/fon.jpg') }}" alt="#">
    <div class="login-container">
        <div class="login-box">
            <h2>Добро пожаловать!</h2>
            <p class="subtitle">Войдите в свой аккаунт</p>
            
            @if($errors->any())
                <div class="alert alert-error">
                    {{ $errors->first('email') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf
                
                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Alternate-email SVG Icon</title><path fill="currentColor" d="M12 22q-2.075 0-3.9-.788t-3.175-2.137T2.788 15.9T2 12t.788-3.9t2.137-3.175T8.1 2.788T12 2t3.9.788t3.175 2.137T21.213 8.1T22 12v1.45q0 1.475-1.012 2.513T18.5 17q-.875 0-1.65-.375t-1.3-1.075q-.725.725-1.638 1.088T12 17q-2.075 0-3.537-1.463T7 12t1.463-3.537T12 7t3.538 1.463T17 12v1.45q0 .65.425 1.1T18.5 15t1.075-.45t.425-1.1V12q0-3.35-2.325-5.675T12 4T6.325 6.325T4 12t2.325 5.675T12 20h5v2zm0-7q1.25 0 2.125-.875T15 12t-.875-2.125T12 9t-2.125.875T9 12t.875 2.125T12 15"/></svg></span>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               placeholder="your@email.com" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus>
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Пароль</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Password-minimalistic-broken SVG Icon</title><g fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M22 12c0 3.771 0 5.657-1.172 6.828C19.657 20 17.771 20 14 20h-4c-3.771 0-5.657 0-6.828-1.172C2 17.657 2 15.771 2 12c0-3.771 0-5.657 1.172-6.828C4.343 4 6.229 4 10 4h4c3.771 0 5.657 0 6.828 1.172c.654.653.943 1.528 1.07 2.828"/><path fill="currentColor" d="M9 12a1 1 0 1 1-2 0a1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0a1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0a1 1 0 0 1 2 0"/></g></svg></span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               placeholder="••••••••" 
                               required>
                    </div>
                </div>

                <!-- <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Запомнить меня</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-password">Забыли пароль?</a>
                </div> -->

                <button type="submit" class="btn-login">
                    <span>Войти</span>
                </button>

                <div class="register-link">
                    Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>