
@php
use Illuminate\Support\Facades\Auth;
@endphp

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Мероприятия') - Культурный портал</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">

    <!-- Основные стили -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Дополнительные стили для конкретных страниц -->
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
</head>
<body>
    <!-- Хедер -->
    <header class="header">
        <div class="header-container">
            <!-- Навигация (десктоп) -->
            <nav class="nav-menu" id="navMenu">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    Главная
                </a>
                <a href="{{ route('concerts') }}" class="nav-link {{ request()->routeIs('concerts') ? 'active' : '' }}">
                    Концерты
                </a>
                <a href="{{ route('festivals') }}" class="nav-link {{ request()->routeIs('festivals') ? 'active' : '' }}">
                    Фестивали
                </a>
            </nav>

            <!-- Логотип и мобильное меню -->
            <div class="header-left">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <a href="{{ route('home') }}" class="logo">
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

            <!-- Поиск -->
            <div class="header-right">
                <div class="search-container" id="searchContainer">
                    <form action="{{ route('search') }}" method="GET" class="search-form" id="searchForm">
                        <input type="hidden" name="type" id="searchType" value="{{ 
                            request()->routeIs('concerts') ? 'concert' : 
                            (request()->routeIs('festivals') ? 'festival' : 'all') 
                        }}">
                        <input type="text" 
                            name="q" 
                            placeholder="{{ 
                                request()->routeIs('concerts') ? 'Поиск по концертам...' : 
                                (request()->routeIs('festivals') ? 'Поиск по фестивалям...' : 'Поиск мероприятий...') 
                            }}" 
                            class="search-input"
                            value="{{ request('q') }}">
                        <button type="submit" class="search-button">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.9536 14.9458L21 21M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>
                </div>
            <!-- Иконка выбора города -->
            <div class="city-selector">
                <button class="city-button" id="cityButton">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 21C15.5 17.4 19 14.1764 19 10.2C19 6.22355 15.866 3 12 3C8.13401 3 5 6.22355 5 10.2C5 14.1764 8.5 17.4 12 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                
                <div class="city-dropdown" id="cityDropdown">
                    <div class="city-dropdown-header">
                        <span class="city-dropdown-title">Выберите город</span>
                        <input type="text" class="city-search" id="citySearch" placeholder="Поиск города...">
                    </div>
                    <div class="city-list" id="cityList">
                        <!-- Список городов будет загружаться через AJAX -->
                        <div class="city-loading">Загрузка...</div>
                    </div>
                </div>
            </div>
                <!-- Профиль -->
                @auth
                    <div class="profile-dropdown">
                        <button class="profile-button" id="profileButton">
                            <div class="profile-avatar">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="avatar-image" id="avatarImage">
                                @else
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                @endif
                            </div>
                            <span class="profile-name">{{ auth()->user()->name }}</span>
                            <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        
                        <div class="dropdown-menu" id="dropdownMenu">
                            <div class="dropdown-header">
                                <div class="dropdown-avatar">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="avatar-image" id="avatarImage">
                                    @else
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="dropdown-user-info">
                                    <span class="dropdown-user-name">{{ auth()->user()->name }}</span>
                                    <span class="dropdown-user-role">{{ auth()->user()->role->display_name }}</span>
                                </div>
                            </div>
                            
                                <a href="{{ route('profile.orders') }}" class="dropdown-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><title>Ticket SVG Icon</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 1 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 1 1 0-4V7a2 2 0 0 0-2-2z"/></svg>
                                    Мои заказы
                                @if(auth()->user()->orders()->where('status', 'pending')->count() > 0)
                                    <span class="item-badge">{{ auth()->user()->orders()->where('status', 'pending')->count() }}</span>
                                @endif
                            </a>


                            <a href="{{ route('favorites.list') }}" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 48 48"><title>Like SVG Icon</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 8C8.925 8 4 12.925 4 19c0 11 13 21 20 23.326C31 40 44 30 44 19c0-6.075-4.925-11-11-11c-3.72 0-7.01 1.847-9 4.674A10.987 10.987 0 0 0 15 8"/></svg>
                                Избранное
                                @if(Auth::user()->favorites()->count() > 0)
                                    <span class="item-badge">{{ Auth::user()->favorites()->count() }}</span>
                                @endif
                            </a>


                            @if(auth()->user()->isOrganizer() || auth()->user()->isAdmin())
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('organizer.events') }}" class="dropdown-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><title>Calendar SVG Icon</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2      "><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><path d="M16 2v4M8 2v4m-5 4h18"/></g></svg>
                                    Мероприятия
                                </a>
                                <a href="{{ route('organizer.create') }}" class="dropdown-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><title>Plus SVG Icon</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/></svg>
                                    Создать мероприятие
                                </a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('admin.users') }}" class="dropdown-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 36 36"><title>Administrator Line SVG Icon</title><path fill="currentColor" d="M14.68 14.81a6.76 6.76 0 1 1 6.76-6.75a6.77 6.77 0 0 1-6.76 6.75m0-11.51a4.76 4.76 0 1 0 4.76 4.76a4.76 4.76 0 0 0-4.76-4.76" class="clr-i-outline clr-i-outline-path-1"/><path fill="currentColor" d="M16.42 31.68A2.14 2.14 0 0 1 15.8 30H4v-5.78a14.8 14.8 0 0 1 11.09-4.68h.72a2.2 2.2 0 0 1 .62-1.85l.12-.11c-.47 0-1-.06-1.46-.06A16.47 16.47 0 0 0 2.2 23.26a1 1 0 0 0-.2.6V30a2 2 0 0 0 2 2h12.7Z" class="clr-i-outline clr-i-outline-path-2"/><path fill="currentColor" d="M26.87 16.29a.4.4 0 0 1 .15 0a.4.4 0 0 0-.15 0" class="clr-i-outline clr-i-outline-path-3"/><path fill="currentColor" d="m33.68 23.32l-2-.61a7.2 7.2 0 0 0-.58-1.41l1-1.86A.38.38 0 0 0 32 19l-1.45-1.45a.36.36 0 0 0-.44-.07l-1.84 1a7 7 0 0 0-1.43-.61l-.61-2a.36.36 0 0 0-.36-.24h-2.05a.36.36 0 0 0-.35.26l-.61 2a7 7 0 0 0-1.44.6l-1.82-1a.35.35 0 0 0-.43.07L17.69 19a.38.38 0 0 0-.06.44l1 1.82a6.8 6.8 0 0 0-.63 1.43l-2 .6a.36.36 0 0 0-.26.35v2.05A.35.35 0 0 0 16 26l2 .61a7 7 0 0 0 .6 1.41l-1 1.91a.36.36 0 0 0 .06.43l1.45 1.45a.38.38 0 0 0 .44.07l1.87-1a7 7 0 0 0 1.4.57l.6 2a.38.38 0 0 0 .35.26h2.05a.37.37 0 0 0 .35-.26l.61-2.05a7 7 0 0 0 1.38-.57l1.89 1a.36.36 0 0 0 .43-.07L32 30.4a.35.35 0 0 0 0-.4l-1-1.88a7 7 0 0 0 .58-1.39l2-.61a.36.36 0 0 0 .26-.35v-2.1a.36.36 0 0 0-.16-.35M24.85 28a3.34 3.34 0 1 1 3.33-3.33A3.34 3.34 0 0 1 24.85 28" class="clr-i-outline clr-i-outline-path-4"/><path fill="none" d="M0 0h36v36H0z"/></svg>
                                    Админ-панель
                                </a>
                            @endif
                            <!-- 
                            @if(auth()->user()->isAdmin())
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('admin.users') }}" class="dropdown-item">
                                    <span class="item-icon">👥</span>
                                    Пользователи
                                </a>
                                <a href="{{ route('admin.events') }}" class="dropdown-item">
                                    <span class="item-icon">📅</span>
                                    Все мероприятия
                                </a>
                                <a href="{{ route('admin.roles') }}" class="dropdown-item">
                                    <span class="item-icon">⚙️</span>
                                    Роли
                                </a>
                            @endif -->
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><title>Settings SVG Icon</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83a2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33a1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2a2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0a2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2a2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83a2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2a2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0a2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2a2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1"/></g></svg>
                                Редактировать профиль
                            </a>
                            <div class="dropdown-divider"></div>
                            
                            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                                @csrf
                                <button type="submit" class="dropdown-item logout-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 512 512"><title>Log-in SVG Icon</title><path fill="#3f3f3fff" d="M392 80H232a56.06 56.06 0 0 0-56 56v104h153.37l-52.68-52.69a16 16 0 0 1 22.62-22.62l80 80a16 16 0 0 1 0 22.62l-80 80a16 16 0 0 1-22.62-22.62L329.37 272H176v104c0 32.05 33.79 56 64 56h152a56.06 56.06 0 0 0 56-56V136a56.06 56.06 0 0 0-56-56M80 240a16 16 0 0 0 0 32h96v-32Z"/></svg>
                                    Выйти
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="auth-buttons">
                        <a href="{{ route('login') }}" class="btn-login">Войти</a>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Мобильное меню (скрыто по умолчанию) -->
        <div class="mobile-menu" id="mobileMenu">
                        
            <nav class="mobile-nav">
                <a href="{{ route('concerts') }}" class="mobile-nav-link">
                    Концерты
                </a>
                <a href="{{ route('festivals') }}" class="mobile-nav-link">
                    Фестивали
                </a>
                <a href="{{ route('home') }}" class="mobile-nav-link">
                    Главная
                </a>
            </nav>
        </div>
    </header>

    <!-- Основной контент -->
    <main class="main-content">
        
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
                <button class="alert-close" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
                <button class="alert-close" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Футер -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>Event</h4>
                <p>Покупайте билеты на лучшие мероприятия быстро и небезопасно</p>
                
            </div>
            
            <div class="footer-section">
                <h4>Мероприятия</h4>
                <a href="{{ route('concerts') }}" class="footer-link">Концерты</a>
                <a href="{{ route('festivals') }}" class="footer-link">Фестивали</a>
                <a href="#" class="footer-link">Спорт</a>
                <a href="#" class="footer-link">Театр</a>
            </div>
            
            <div class="footer-section">
                <h4>Поддержка</h4>
                <a href="#" class="footer-link">Часто задаваемые вопросы</a>
                <a href="#" class="footer-link">Как купить билет</a>
                <a href="#" class="footer-link">Возврат билетов</a>
                <a href="#" class="footer-link">Связаться с нами</a>
            </div>
            
            <div class="footer-section">
                <h4>Контакты</h4>
                <p class="contact-info"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Telephone SVG Icon</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.6 14.522c-2.395 2.52-8.504-3.534-6.1-6.064c1.468-1.545-.19-3.31-1.108-4.609c-1.723-2.435-5.504.927-5.39 3.066c.363 6.746 7.66 14.74 14.726 14.042c2.21-.218 4.75-4.21 2.214-5.669c-1.267-.73-3.008-2.17-4.342-.767"/></svg> 8 (800) 2000 122</p>
                <p class="contact-info"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Alternate-email SVG Icon</title><path fill="currentColor" d="M12 22q-2.075 0-3.9-.788t-3.175-2.137T2.788 15.9T2 12t.788-3.9t2.137-3.175T8.1 2.788T12 2t3.9.788t3.175 2.137T21.213 8.1T22 12v1.45q0 1.475-1.012 2.513T18.5 17q-.875 0-1.65-.375t-1.3-1.075q-.725.725-1.638 1.088T12 17q-2.075 0-3.537-1.463T7 12t1.463-3.537T12 7t3.538 1.463T17 12v1.45q0 .65.425 1.1T18.5 15t1.075-.45t.425-1.1V12q0-3.35-2.325-5.675T12 4T6.325 6.325T4 12t2.325 5.675T12 20h5v2zm0-7q1.25 0 2.125-.875T15 12t-.875-2.125T12 9t-2.125.875T9 12t.875 2.125T12 15"/></svg> support@event.ru</p>
                <p class="contact-info"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><title>Add-location-alt-outline SVG Icon</title><path fill="currentColor" d="M12 22q-4.025-3.425-6.012-6.362T4 10.2q0-3.75 2.413-5.975T12 2h.5q.25 0 .5.05v2.025q-.25-.05-.488-.063T12 4Q9.475 4 7.738 5.738T6 10.2q0 1.775 1.475 4.063T12 19.35q3.05-2.8 4.525-5.087T18 10.2V10h2v.2q0 2.5-1.987 5.438T12 22m0-10q.825 0 1.413-.587T14 10t-.587-1.412T12 8t-1.412.588T10 10t.588 1.413T12 12m6-4h2V5h3V3h-3V0h-2v3h-3v2h3z"/></svg> Пермь, ул. Лодыгина, 10</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Events. Все права защищены.</p>
        </div>
    </footer>

    <!-- Скрипты -->
    <script>
        // Дропдаун профиля
        const profileButton = document.getElementById('profileButton');
        const dropdownMenu = document.getElementById('dropdownMenu');
        
        if (profileButton && dropdownMenu) {
            profileButton.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });
            
            document.addEventListener('click', (e) => {
                if (!profileButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }

        // Мобильное меню
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileToggle && mobileMenu) {
            mobileToggle.addEventListener('click', () => {
                mobileToggle.classList.toggle('active');
                mobileMenu.classList.toggle('show');
                document.body.classList.toggle('menu-open');
            });
        }

        // Автоматическое скрытие уведомлений
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });

        // Анимация появления контента
        document.addEventListener('DOMContentLoaded', () => {
            document.body.style.opacity = '1';
        });
    </script>
    <script>
    // Глобальная функция для добавления/удаления из избранного
    window.toggleFavorite = function(eventId, button) {
        const icon = button.querySelector('span');
        const isFavorite = icon.textContent === '❤️';
        
        // Блокируем кнопку на время запроса
        button.disabled = true;
        button.style.opacity = '0.5';
        
        // Определяем URL и метод
        const url = isFavorite ? `/favorites/remove/${eventId}` : `/favorites/add/${eventId}`;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Меняем иконку
                icon.textContent = isFavorite ? '🤍' : '❤️';
                button.classList.toggle('active');
                
                // Показываем уведомление
                showNotification(data.message, 'success');
                
                // Обновляем счетчик в хедере
                const counter = document.getElementById('favoritesCount');
                if (counter) {
                    counter.textContent = data.favorites_count;
                }
                
                // Обновляем всплывающее окно избранного, если оно открыто
                const popupContent = document.getElementById('favoritesPopupContent');
                if (popupContent && data.html) {
                    popupContent.innerHTML = data.html;
                }
            } else {
                showNotification(data.message || 'Произошла ошибка', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Произошла ошибка при выполнении запроса', 'error');
        })
        .finally(() => {
            // Разблокируем кнопку
            button.disabled = false;
            button.style.opacity = '1';
        });
    };

    // Функция для удаления из избранного (из всплывающего окна)
    window.removeFromFavorites = function(eventId) {
        const button = event.currentTarget;
        
        fetch(`/favorites/remove/${eventId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновляем кнопки на странице
                const buttons = document.querySelectorAll(`.favorite-btn[data-event-id="${eventId}"]`);
                buttons.forEach(btn => {
                    const icon = btn.querySelector('span');
                    if (icon) {
                        icon.textContent = '🤍';
                        btn.classList.remove('active');
                    }
                });
                
                // Обновляем всплывающее окно
                const popupContent = document.getElementById('favoritesPopupContent');
                if (popupContent && data.html) {
                    popupContent.innerHTML = data.html;
                }
                
                // Обновляем счетчик
                const counter = document.getElementById('favoritesCount');
                if (counter) {
                    counter.textContent = data.favorites_count;
                }
                
                showNotification('Удалено из избранного', 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Произошла ошибка', 'error');
        });
    };
// Выбор города
const cityButton = document.getElementById('cityButton');
const cityDropdown = document.getElementById('cityDropdown');
const citySearch = document.getElementById('citySearch');
const cityList = document.getElementById('cityList');

// Загрузка популярных городов при открытии
let popularCities = [];

// Функция для загрузки городов из мероприятий
async function loadCities() {
    try {
        const response = await fetch('/api/cities');
        const data = await response.json();
        popularCities = data.cities;
        renderCities(popularCities);
    } catch (error) {
        console.error('Error loading cities:', error);
        cityList.innerHTML = '<div class="city-no-results">Ошибка загрузки</div>';
    }
}

// Функция для отображения городов
function renderCities(cities) {
    if (cities.length === 0) {
        cityList.innerHTML = '<div class="city-no-results">Города не найдены</div>';
        return;
    }
    
    // Получаем текущий выбранный город из localStorage или сессии
    const currentCity = localStorage.getItem('selectedCity') || 'all';
    
    let html = '';
    
    // Добавляем пункт "Все города"
    html += `
        <div class="city-item ${currentCity === 'all' ? 'selected' : ''}" onclick="selectCity('all')">
            Все города
        </div>
    `;
    
    cities.forEach(city => {
        html += `
            <div class="city-item ${city === currentCity ? 'selected' : ''}" onclick="selectCity('${city}')">
                ${city}
            </div>
        `;
    });
    
    cityList.innerHTML = html;
}


// Определяем цвет фона под хедером
function checkHeaderBackground() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const header = document.querySelector('.header');
    const scrollY = window.scrollY;
    
    // Если проскроллили больше 50px - добавляем класс scrolled
    if (scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
        
        // Проверяем яркость фона под хедером
        const heroSection = document.querySelector('.event-hero, .page-hero, .calendar-section');
        if (heroSection) {
            const rect = heroSection.getBoundingClientRect();
            const headerRect = header.getBoundingClientRect();
            
            // Если хедер пересекается с темной секцией
            if (rect.top < headerRect.bottom && rect.bottom > headerRect.top) {
                // Можно добавить логику определения яркости
                // или просто использовать белый текст
                header.classList.add('white-text');
            } else {
                header.classList.remove('white-text');
            }
            if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', function() {
            if (mobileMenu.classList.contains('show')) {
                // Когда меню закрыто - возвращаем исходные стили
                header.classList.remove('mobile-menu-open');
                header.classList.remove('white-text');
            } else {
                // Когда меню открыто - меняем иконки на черные
                header.classList.add('mobile-menu-open');
                header.classList.add('white-text');
            }
        });
    }
        }
    }
}

// Слушаем скролл
window.addEventListener('scroll', checkHeaderBackground);
window.addEventListener('load', checkHeaderBackground);




// Функция выбора города
window.selectCity = function(city) {
    // Сохраняем в localStorage
    localStorage.setItem('selectedCity', city);
    
    // Отправляем на сервер для сохранения в сессии
    fetch('/api/select-city', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ city: city })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Обновляем заголовок
            updateCityTitle(city);
            
            // Перезагружаем страницу для применения фильтра
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при выборе города', 'error');
    });
    
    // Закрываем дропдаун
    cityDropdown.classList.remove('show');
}

// Функция обновления заголовка
function updateCityTitle(city) {
    const titleElement = document.querySelector('.city-title');
    if (titleElement) {
        // Определяем текущую страницу по URL
        const path = window.location.pathname;
        
        let pageName = 'Мероприятия';
        
        if (path.includes('/concerts')) {
            pageName = 'Концерты';
        } else if (path.includes('/festivals')) {
            pageName = 'Фестивали';
        } else if (path.includes('/')) {
            // Для главной страницы
            pageName = 'Мероприятия';
        }
        
        if (city && city !== 'all' && city !== null) {
            titleElement.textContent = `${pageName} • ${city}`;
        } else {
            titleElement.textContent = pageName;
        }
    }
}

// Поиск городов
if (citySearch) {
    citySearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        if (searchTerm.length < 2) {
            renderCities(popularCities);
            return;
        }
        
        const filtered = popularCities.filter(city => 
            city.toLowerCase().includes(searchTerm)
        );
        renderCities(filtered);
    });
}

// Открытие/закрытие дропдауна
if (cityButton && cityDropdown) {
    cityButton.addEventListener('click', (e) => {
        e.stopPropagation();
        cityDropdown.classList.toggle('show');
        
        if (cityDropdown.classList.contains('show')) {
            loadCities();
            citySearch.value = '';
            citySearch.focus();
        }
    });
    
    document.addEventListener('click', (e) => {
        if (!cityButton.contains(e.target) && !cityDropdown.contains(e.target)) {
            cityDropdown.classList.remove('show');
        }
    });
}

// Инициализация заголовка при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const selectedCity = localStorage.getItem('selectedCity');
    updateCityTitle(selectedCity);
});
    // Функция для показа уведомлений
    window.showNotification = function(message, type = 'info') {
        // Проверяем, есть ли уже контейнер для уведомлений
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
        notification.className = `notification notification-${type}`;
        
        // Цвета для разных типов
        const colors = {
            success: '#252525ff',
            error: '#f44336',
            warning: '#ff9800',
            info: '#2196f3'
        };
        
        notification.style.cssText = `
            background: ${colors[type] || colors.info};
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease;
            cursor: pointer;
            min-width: 250px;
        `;
        notification.textContent = message;
        
        container.appendChild(notification);
        
        // Удаляем через 3 секунды
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
        
        // Добавляем стили для анимаций, если их еще нет
        if (!document.getElementById('notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
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
        }
    };

    // Функция для открытия/закрытия попапа с избранным
    window.toggleFavoritesPopup = function() {
        const popup = document.getElementById('favoritesPopup');
        if (popup) {
            if (popup.style.display === 'none' || popup.style.display === '') {
                popup.style.display = 'block';
                // Обновляем содержимое попапа
                fetch('/favorites/popup', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const popupContent = document.getElementById('favoritesPopupContent');
                    if (popupContent) {
                        popupContent.innerHTML = html;
                    }
                });
            } else {
                popup.style.display = 'none';
            }
        }
    };

    // Закрытие попапа при клике вне его
    document.addEventListener('DOMContentLoaded', function() {
        const favoritesToggle = document.getElementById('favoritesToggle');
        const favoritesPopup = document.getElementById('favoritesPopup');
        
        if (favoritesToggle && favoritesPopup) {
            favoritesToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleFavoritesPopup();
            });
            
            document.addEventListener('click', function(e) {
                if (!favoritesToggle.contains(e.target) && !favoritesPopup.contains(e.target)) {
                    favoritesPopup.style.display = 'none';
                }
            });
        }
    });
</script>
    @stack('scripts')


    <button id="scrollToTopBtn" class="scroll-to-top" aria-label="Наверх">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="18 15 12 9 6 15"></polyline>
    </svg>
</button>

<style>
/* Стили для кнопки "Наверх" */
.scroll-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #ECF86E;
    color: #000;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px #eef5a185;
    transition: all 0.3s ease;
    opacity: 0;
    visibility: hidden;
    z-index: 999;
}

.scroll-to-top:hover {
    background: #f0ff51ff;
    transform: translateY(-5px);
    box-shadow: 0 6px 20px #f4ff7b85;
}

.scroll-to-top.show {
    opacity: 1;
    visibility: visible;
}

.scroll-to-top svg {
    width: 24px;
    height: 24px;
    transition: transform 0.3s ease;
}

.scroll-to-top:hover svg {
    transform: translateY(-3px);
}

/* Анимация появления */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.scroll-to-top.show {
    animation: fadeInUp 0.3s ease;
}

/* Для мобильных устройств */
@media (max-width: 768px) {
    .scroll-to-top {
        bottom: 20px;
        right: 20px;
        width: 45px;
        height: 45px;
    }
    
    .scroll-to-top svg {
        width: 20px;
        height: 20px;
    }
}
</style>

<script>
// Кнопка "Наверх"
document.addEventListener('DOMContentLoaded', function() {
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    
    if (scrollToTopBtn) {
        // Показываем/скрываем кнопку при скролле
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });
        
        // Плавный скролл наверх при клике
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
</script>
</body>
</html>