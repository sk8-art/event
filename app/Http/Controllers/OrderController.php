<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Создание заказа
     */
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $ticketType = TicketType::findOrFail($request->ticket_type_id);
        
        // Проверяем, принадлежит ли тип билета этому мероприятию
        if ($ticketType->event_id !== $event->id) {
            return back()->with('error', 'Некорректный тип билета');
        }
        
        // Проверяем наличие билетов
        if ($ticketType->available < $request->quantity) {
            return back()->with('error', 'Недостаточно билетов выбранного типа');
        }
        
        // Проверяем, можно ли купить билеты
        if (!$event->canBuyTickets()) {
            return back()->with('error', 'Билеты на это мероприятие уже нельзя купить');
        }

        // Создаем заказ
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id, // теперь это поле существует
            'quantity' => $request->quantity,
            'unit_price' => $ticketType->price,
            'total_price' => $ticketType->price * $request->quantity,
            'status' => Order::STATUS_PENDING,
        ]);
        
        // Уменьшаем количество доступных билетов
        $ticketType->decrement('available', $request->quantity);
        
        // Также уменьшаем общее количество доступных билетов в событии
        $event->decrement('available_tickets', $request->quantity);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Заказ создан!');
    }

    /**
     * Просмотр заказа
     */
    public function show(Order $order)
    {
        // Проверяем, что заказ принадлежит пользователю или пользователь - админ
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Загружаем связанное мероприятие
        $order->load('event');

        return view('orders.show', compact('order'));
    }

    /**
     * Отмена заказа
     */
    public function cancel(Order $order)
    {
        // Проверяем, что заказ принадлежит пользователю
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // ПОЛУЧАЕМ МЕРОПРИЯТИЕ
        $event = $order->event;
        
        // ПРОВЕРКА: Не началось ли мероприятие?
        if ($event && $event->date <= now()) {
            return back()->with('error', 'Нельзя отменить заказ - мероприятие уже началось или закончилось');
        }

        // Проверяем, можно ли отменить по статусу
        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Этот заказ нельзя отменить');
        }

        // Возвращаем билеты
        if ($order->ticket_type_id) {
            // Если есть тип билета, возвращаем билеты конкретного типа
            $ticketType = TicketType::find($order->ticket_type_id);
            if ($ticketType) {
                $ticketType->increment('available', $order->quantity);
            }
        }
        
        // Возвращаем общее количество билетов в событие
        $event->increment('available_tickets', $order->quantity);

        // Отменяем заказ
        $order->cancel('Отменен пользователем');
        
        return redirect()->route('profile.orders')
            ->with('success', 'Заказ успешно отменен');
    }

    /**
     * Страница оплаты
     */
    public function payment(Order $order)
    {
        // Проверяем, что заказ принадлежит пользователю
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // ПРОВЕРКА: Не началось ли мероприятие?
        $event = $order->event;
        if ($event && $event->date <= now()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Нельзя оплатить - мероприятие уже началось');
        }

        if (!$order->canBePaid()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Этот заказ нельзя оплатить');
        }

        return view('orders.payment', compact('order'));
    }

    /**
     * Обработка оплаты
     */
    public function processPayment(Request $request, Order $order)
    {
        // Проверяем, что заказ принадлежит пользователю
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // ПРОВЕРКА: Не началось ли мероприятие?
        $event = $order->event;
        if ($event && $event->date <= now()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Нельзя оплатить - мероприятие уже началось');
        }

        // Валидация
        $request->validate([
            'payment_method' => 'required|in:card,online,cash',
        ]);

        // Проверяем, можно ли оплатить
        if (!$order->canBePaid()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Этот заказ нельзя оплатить');
        }

        // Имитация обработки платежа
        try {
            // Здесь должна быть реальная интеграция с платежной системой
            
            // Для демонстрации просто ждем 1 секунду
            sleep(1);
            
            // Генерируем ID транзакции
            $paymentId = 'PAY_' . strtoupper(uniqid());
            
            // Отмечаем заказ как оплаченный
            $order->markAsPaid($request->payment_method, $paymentId);
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Оплата прошла успешно! Номер транзакции: ' . $paymentId);
                
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при оплате: ' . $e->getMessage());
        }
    }

    /**
     * Проверка статуса оплаты (опционально)
     */
    public function checkPayment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $order->status,
            'paid' => in_array($order->status, ['paid', 'confirmed']),
            'payment_method' => $order->payment_method,
            'paid_at' => $order->paid_at,
        ]);
    }

    /**
     * Скачивание билетов
     */
    public function downloadTickets(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($order->status, ['paid', 'confirmed'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Билеты доступны только для оплаченных заказов');
        }

        // Здесь будет логика генерации PDF
        return redirect()->route('orders.show', $order)
            ->with('info', 'Функция скачивания билетов находится в разработке');
    }


    /**
     * Автоматическая отмена заказа по истечении времени
     */
    public function autoCancel(Order $order)
    {
        try {
        // Проверяем, что заказ принадлежит пользователю
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false, 
                'message' => 'Нет доступа'
            ], 403);
        }
        
        // Проверяем, что заказ еще в статусе pending
        if ($order->status !== Order::STATUS_PENDING) {
            return response()->json([
                'success' => false, 
                'message' => 'Заказ уже обработан'
            ]);
        }
        
        // Проверяем, что прошло больше 15 минут
        if ($order->created_at->diffInMinutes(now()) < 15) {
            return response()->json([
                'success' => false, 
                'message' => 'Еще рано отменять'
            ]);
        }
        
        // Сохраняем количество билетов до отмены для проверки
        $beforeTickets = $order->event->available_tickets;
        
        // Отменяем заказ и возвращаем билеты
        $order->cancel('Автоматическая отмена по истечении времени оплаты');
        
        // Проверяем, увеличилось ли количество билетов
        $afterTickets = $order->event->fresh()->available_tickets;
        
        \Log::info('Автоматическая отмена заказа', [
            'order_id' => $order->id,
            'quantity' => $order->quantity,
            'tickets_before' => $beforeTickets,
            'tickets_after' => $afterTickets,
            'diff' => $afterTickets - $beforeTickets
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Заказ отменен, билеты возвращены',
            'debug' => [
                'before' => $beforeTickets,
                'after' => $afterTickets,
                'returned' => $order->quantity
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Ошибка при автоматической отмене заказа: ' . $e->getMessage(), [
            'order_id' => $order->id,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Ошибка сервера: ' . $e->getMessage()
        ], 500);
    }
    }


    /**
     * Возврат заказа
     */
    public function refund(Order $order)
    {
        // Проверяем, что заказ принадлежит пользователю
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Проверяем, можно ли вернуть
        if (!$order->canBeRefunded()) {
            return back()->with('error', 'Этот заказ нельзя вернуть');
        }

        // Возвращаем билеты
        if ($order->ticket_type_id) {
            $ticketType = TicketType::find($order->ticket_type_id);
            if ($ticketType) {
                $ticketType->increment('available', $order->quantity);
            }
        }
        
        // Возвращаем общее количество билетов в событие
        $event = $order->event;
        if ($event) {
            $event->increment('available_tickets', $order->quantity);
        }

        // Обновляем статус заказа
        $order->update([
            'status' => Order::STATUS_REFUNDED,
            'notes' => ($order->notes ? $order->notes . "\n" : '') . 'Возврат средств ' . now()->format('d.m.Y H:i')
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Билеты возвращены. Деньги поступят на карту в течение 3-5 рабочих дней.');
    }



    //CSV
    public function exportOrdersCSV($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        // Проверка прав
        if ($event->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $orders = Order::where('event_id', $eventId)
            ->with('user', 'ticketType')
            ->get();

        $fileName = 'orders_' . $event->title . '_' . Carbon::now()->format('d-m-Y') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Добавляем BOM для корректного отображения кириллицы в Excel
            fwrite($file, "\xEF\xBB\xBF");
            
            // Заголовки
            fputcsv($file, [
                '№ заказа', 'Дата заказа', 'Статус', 'Покупатель', 'Email', 'Телефон',
                'Тип билета', 'Количество', 'Цена за билет', 'Итого', 'Способ оплаты', 'Дата оплаты'
            ], ';');
            
            // Данные
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d.m.Y H:i'),
                    $order->status_name,
                    $order->customer_name ?? $order->user->name ?? '—',
                    $order->customer_email ?? $order->user->email ?? '—',
                    $order->customer_phone ?? '—',
                    $order->ticketType->name ?? 'Стандартный',
                    $order->quantity,
                    number_format($order->unit_price, 0, ',', ' ') . ' ₽',
                    number_format($order->total_price, 0, ',', ' ') . ' ₽',
                    $order->payment_method_name ?? '—',
                    $order->paid_at ? $order->paid_at->format('d.m.Y H:i') : '—'
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}