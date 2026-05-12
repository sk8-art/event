<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Добавить в избранное
     */
    public function add(Event $event)
    {
        $user = Auth::user();
        
        // Проверяем, не добавлено ли уже в избранное
        if ($user->hasInFavorites($event->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Мероприятие уже в избранном'
            ]);
        }
        
        // Добавляем в избранное
        Favorite::create([
            'user_id' => $user->id,
            'event_id' => $event->id
        ]);
        
        // Получаем обновленный список избранного
        $favorites = $user->favoriteEvents()->latest()->limit(5)->get();
        
        // Рендерим HTML для всплывающего окна
        $html = view('components.favorites-popup', compact('favorites'))->render();
        
        return response()->json([
            'success' => true,
            'message' => 'Добавлено в избранное',
            'favorites_count' => $user->favorites()->count(),
            'html' => $html
        ]);
    }

    /**
     * Удалить из избранного
     */
    public function remove(Event $event)
    {
        $user = Auth::user();
        
        Favorite::where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->delete();
        
        // Получаем обновленный список избранного
        $favorites = $user->favoriteEvents()->latest()->limit(5)->get();
        
        // Рендерим HTML для всплывающего окна
        $html = view('components.favorites-popup', compact('favorites'))->render();
        
        return response()->json([
            'success' => true,
            'message' => 'Удалено из избранного',
            'favorites_count' => $user->favorites()->count(),
            'html' => $html
        ]);
    }

    /**
     * Получить список избранного
     */
    public function list()
    {
        $user = Auth::user();
        $favorites = $user->favoriteEvents()->latest()->paginate(12);
        
        return view('favorites.index', compact('favorites'));
    }

    /**
     * Получить всплывающее окно с избранным
     */
    public function popup()
    {
        $user = Auth::user();
        $favorites = $user->favoriteEvents()->latest()->limit(5)->get();
        
        return view('components.favorites-popup', compact('favorites'));
    }
}