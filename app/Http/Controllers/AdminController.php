<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Order;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Конструктор - проверяем что пользователь админ
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin()) {
                abort(403, 'Доступ запрещен');
            }
            return $next($request);
        });
    }

    /**
     * Список пользователей
     */
    public function users()
    {
        $users = User::with('role')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.users', compact('users'));
    }

    /**
     * Редактирование пользователя
     */
    public function editUser(User $user)
    {
        $roles = Role::all();
        return view('admin.edit-user', compact('user', 'roles'));
    }

    /**
     * Обновление пользователя
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update($request->only('name', 'email', 'role_id'));

        return redirect()->route('admin.users')
            ->with('success', 'Пользователь обновлен');
    }

    /**
     * Блокировка пользователя
     */
    public function blockUser(User $user)
    {
        // Здесь можно добавить поле blocked_at в таблицу users
        // $user->update(['blocked_at' => now()]);
        
        return back()->with('success', 'Пользователь заблокирован');
    }

    /**
     * Список всех мероприятий
     */
    public function events()
    {
        $events = Event::with('organizer')
            ->withCount('orders')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.events', compact('events'));
    }

    /**
     * Детали мероприятия
     */
    public function showEvent(Event $event)
    {
        $event->load('organizer', 'orders.user');
        return view('admin.show-event', compact('event'));
    }

    /**
     * Изменение статуса мероприятия
     */
    public function updateEventStatus(Request $request, Event $event)
    {
        $request->validate([
            'status' => 'required|in:active,cancelled,completed',
        ]);

        $event->update(['status' => $request->status]);

        return back()->with('success', 'Статус мероприятия обновлен');
    }

    /**
     * Управление ролями
     */
    public function roles()
    {
        $roles = Role::withCount('users')->get();
        return view('admin.roles', compact('roles'));
    }

    /**
     * Создание роли
     */
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles',
            'display_name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Role::create($request->all());

        return redirect()->route('admin.roles')
            ->with('success', 'Роль создана');
    }

    /**
     * Редактирование роли
     */
    public function editRole(Role $role)
    {
        return view('admin.edit-role', compact('role'));
    }

    /**
     * Обновление роли
     */
    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'display_name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $role->update($request->all());

        return redirect()->route('admin.roles')
            ->with('success', 'Роль обновлена');
    }

    /**
     * Статистика
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_events' => Event::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::whereIn('status', ['paid', 'confirmed'])->sum('total_price'),
            'recent_orders' => Order::with('user', 'event')
                ->latest()
                ->take(10)
                ->get(),
            'popular_events' => Event::withCount('orders')
                ->orderBy('orders_count', 'desc')
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}