<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class EventController extends Controller
{
    /**
     * Главная страница с мероприятиями
     */
    public function index()
    {
        $events = Event::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(6)
            ->get();
            
        return view('home', compact('events'));
    }

    /**
     * Страница концертов с фильтрацией
     */
    public function concerts(Request $request)
{
    $query = Event::where('type', 'concert')
        ->where('date', '>=', now());
    

        // Фильтр по городу из сессии или запроса
    $selectedCity = session('selected_city', $request->city);
    if ($selectedCity && $selectedCity !== 'all') {
        $query->where('location', 'LIKE', '%' . $selectedCity . '%');
    }
    // Фильтр по дате - теперь обрабатывает оба варианта
    if ($request->filled('date')) {
        // Проверяем, является ли значение датой в формате Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->date)) {
            // Это конкретная дата из календаря
            $query->whereDate('date', $request->date);
        } else {
            // Это предустановленные фильтры (today, tomorrow, etc.)
            switch ($request->date) {
                case 'today':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'tomorrow':
                    $query->whereDate('date', Carbon::tomorrow());
                    break;
                case 'week':
                    $query->whereBetween('date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('date', Carbon::now()->month)
                          ->whereYear('date', Carbon::now()->year);
                    break;
            }
        }
    }
    
    // Фильтр по локации
    if ($request->filled('location')) {
        $query->where('location', 'LIKE', '%' . $request->location . '%');
    }
    
    // Фильтр по максимальной цене
    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }
    
    // Сортировка
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'date_asc':
                $query->orderBy('date', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('date', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->orderBy('date', 'asc');
        }
    } else {
        $query->orderBy('date', 'asc');
    }
    
    $concerts = $query->paginate(12)->withQueryString();
    
    return view('events.concerts', compact('concerts'));
}

    /**
 * Страница фестивалей с фильтрацией
 */
public function festivals(Request $request)
{
    $query = Event::where('type', 'festival')
        ->where('date', '>=', now());
    
    // ===== ДОБАВЛЕН ФИЛЬТР ПО ГОРОДУ =====
    // Фильтр по городу из сессии
    $selectedCity = session('selected_city');
    if ($selectedCity && $selectedCity !== 'all') {
        $query->where('location', 'LIKE', '%' . $selectedCity . '%');
    }
    // ======================================
    
    // Фильтр по дате
    if ($request->filled('date')) {
        // Добавляем обработку конкретной даты из календаря
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->date)) {
            $query->whereDate('date', $request->date);
        } else {
            switch ($request->date) {
                case 'today':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'tomorrow':
                    $query->whereDate('date', Carbon::tomorrow());
                    break;
                case 'week':
                    $query->whereBetween('date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('date', Carbon::now()->month)
                          ->whereYear('date', Carbon::now()->year);
                    break;
            }
        }
    }
    
    // Фильтр по локации (из формы)
    if ($request->filled('location')) {
        $query->where('location', 'LIKE', '%' . $request->location . '%');
    }
    
    // Фильтр по максимальной цене
    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }
    
    // Сортировка
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'date_asc':
                $query->orderBy('date', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('date', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->orderBy('date', 'asc');
        }
    } else {
        $query->orderBy('date', 'asc');
    }
    
    $festivals = $query->paginate(12)->withQueryString();
    
    return view('events.festivals', compact('festivals'));
}   

    /**
     * Поиск мероприятий
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $selectedCity = session('selected_city', 'all');
        
        $eventsQuery = Event::where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->orWhere('location', 'LIKE', "%{$query}%");
            })
            ->where('date', '>=', now());
        
        // Сначала показываем мероприятия из выбранного города
        if ($selectedCity && $selectedCity !== 'all') {
            $eventsQuery->orderByRaw("CASE WHEN location LIKE ? THEN 0 ELSE 1 END", ['%' . $selectedCity . '%'])
                ->orderBy('date', 'asc');
        } else {
            $eventsQuery->orderBy('date', 'asc');
        }
        
        $events = $eventsQuery->paginate(12);
        
        return view('events.search', compact('events', 'query', 'selectedCity'));
    }

    /**
     * Детальная страница мероприятия
     */
    public function show(Event $event)
    {
        $event->increment('views');
        
        // Загружаем только активные типы билетов с доступными местами
        $event->load(['activeTicketTypes' => function($query) {
            $query->where('available', '>', 0);
        }]);
        
        $similarEvents = Event::where('type', $event->type)
            ->where('id', '!=', $event->id)
            ->where('date', '>=', now())
            ->where('status', 'active')
            ->orderBy('date', 'asc')
            ->limit(4)
            ->get();
        
        $popularEvents = Event::where('id', '!=', $event->id)
            ->where('date', '>=', now())
            ->where('status', 'active')
            ->orderBy('views', 'desc')
            ->limit(4)
            ->get();
        
        return view('events.show', compact('event', 'similarEvents', 'popularEvents'));
    }

    /**
     * Мои мероприятия (для организатора)
     */
    public function myEvents()
    {
        $events = Event::where('organizer_id', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(10);
            
        return view('organizer.events', compact('events'));
    }

    /**
     * Форма создания мероприятия (для организатора)
     */
    public function create()
    {
        return view('organizer.create-event');
    }

    /**
     * Сохранение нового мероприятия (для организатора)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:concert,festival,other',
            'date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'image' => 'required|image|max:2048',
            
            // Валидация билетов
            'standard_tickets' => 'required|integer|min:0',
            'standard_price' => 'required|numeric|min:0',
            'fan_tickets' => 'required|integer|min:0',
            'fan_price' => 'required|numeric|min:0',
            'vip_tickets' => 'required|integer|min:0',
            'vip_price' => 'required|numeric|min:0',
        ]);

        // Подсчитываем общее количество билетов
        $totalTickets = $request->standard_tickets + $request->fan_tickets + $request->vip_tickets;
        
        if ($totalTickets == 0) {
            return back()->with('error', 'Должен быть хотя бы один билет')->withInput();
        }

        // Загрузка изображения
        $imagePath = $request->file('image')->store('events', 'public');

        // Находим минимальную цену для поля price (чтобы было значение)
        $minPrice = PHP_INT_MAX;
        if ($request->standard_tickets > 0 && $request->standard_price < $minPrice) {
            $minPrice = $request->standard_price;
        }
        if ($request->fan_tickets > 0 && $request->fan_price < $minPrice) {
            $minPrice = $request->fan_price;
        }
        if ($request->vip_tickets > 0 && $request->vip_price < $minPrice) {
            $minPrice = $request->vip_price;
        }
        
        if ($minPrice == PHP_INT_MAX) {
            $minPrice = 0;
        }

        // Создаем мероприятие
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'date' => $request->date,
            'location' => $request->location,
            'address' => $request->address,
            'image' => $imagePath,
            'organizer_id' => Auth::id(),
            'status' => 'active',
            'price' => $minPrice, // Добавляем минимальную цену
            'total_tickets' => $totalTickets,
            'available_tickets' => $totalTickets,
        ]);

        // Создаем типы билетов (только если количество > 0)
        $ticketTypes = [];
        
        if ($request->standard_tickets > 0) {
            $ticketTypes[] = [
                'event_id' => $event->id,
                'name' => 'Стандартный',
                'slug' => 'standard',
                'price' => $request->standard_price,
                'quantity' => $request->standard_tickets,
                'available' => $request->standard_tickets,
                'benefits' => json_encode(['Вход на мероприятие', 'Стандартные места']),
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if ($request->fan_tickets > 0) {
            $ticketTypes[] = [
                'event_id' => $event->id,
                'name' => 'Фан-зона',
                'slug' => 'fan',
                'price' => $request->fan_price,
                'quantity' => $request->fan_tickets,
                'available' => $request->fan_tickets,
                'benefits' => json_encode(['Ближе к сцене', 'Отдельный вход', 'Сувенир']),
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if ($request->vip_tickets > 0) {
            $ticketTypes[] = [
                'event_id' => $event->id,
                'name' => 'VIP',
                'slug' => 'vip',
                'price' => $request->vip_price,
                'quantity' => $request->vip_tickets,
                'available' => $request->vip_tickets,
                'benefits' => json_encode(['VIP-лаундж', 'Напитки включены', 'Подарок', 'Лучшие места']),
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Сохраняем типы билетов
        if (!empty($ticketTypes)) {
            DB::table('ticket_types')->insert($ticketTypes);
        }

        return redirect()->route('organizer.events');
    }



    private function getTicketName($slug)
    {
        return [
            'standard' => 'Стандартный',
            'fan' => 'Фан-зона',
            'vip' => 'VIP'
        ][$slug] ?? $slug;
    }

    private function getTicketBenefits($slug)
    {
        $benefits = [
            'standard' => [
                'description' => 'Стандартный вход на мероприятие',
                'list' => ['Вход на мероприятие', 'Стоячие места']
            ],
            'fan' => [
                'description' => 'Лучшие места и дополнительные возможности',
                'list' => ['Вход без очереди', 'Ближе к сцене', 'Сувенирная продукция']
            ],
            'vip' => [
                'description' => 'Максимальный комфорт и привилегии',
                'list' => ['Отдельный вход', 'VIP-лаундж', 'Напитки включены', 'Подарок от организаторов']
            ]
        ];
        
        return $benefits[$slug] ?? $benefits['standard'];
    }
    /**
     * Форма редактирования мероприятия
     */
    public function edit(Event $event)
    {
        // Проверяем, что пользователь является организатором этого мероприятия или админом
        if ($event->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('organizer.edit-event', compact('event'));
    }

    /**
     * Обновление мероприятия
     */
    public function update(Request $request, Event $event)
    {
        // Проверяем, что пользователь является организатором этого мероприятия или админом
        if ($event->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:concert,festival,other',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'total_tickets' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:active,cancelled,completed',
        ]);

        $oldStatus = $event->status;
        $newStatus = $request->status;
        
        $data = $request->except('image');

        // Если загружено новое изображение
        if ($request->hasFile('image')) {
            // Удаляем старое изображение
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);
        
        // Если статус изменился на "cancelled" (отменено)
        if ($oldStatus !== 'cancelled' && $newStatus === 'cancelled') {
            $cancelledOrders = $event->cancelEvent('Мероприятие отменено организатором');
            
            return redirect()->route('organizer.events')
                ->with('warning', "Мероприятие отменено. Автоматически отменено заказов: {$cancelledOrders}");
        }

        return redirect()->route('organizer.events')
            ->with('success', 'Мероприятие успешно обновлено!');
    }
    
}