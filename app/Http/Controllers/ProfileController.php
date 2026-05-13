<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        return redirect()->back();
    }

    public function deleteAvatar()
    {
        $user = Auth::user();
        
        if ($user->avatar) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $user->avatar = null;
            $user->save();
            
            return redirect()->back();
        }

        return redirect()->back();
    }

    public function orders()
    {
        $user = auth()->user();
        
        $orders = Order::where('user_id', $user->id)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $data = [
            'orders' => $orders, 
        ];

        if ($user->isOrganizer() || $user->isAdmin()) {
            $data['myEvents'] = Event::where('organizer_id', $user->id)
                ->withCount('orders')
                ->get(); 
        } else {
            $data['myEvents'] = collect();
        }

        if ($user->isAdmin()) {
            $data['totalUsers'] = User::count() ?: 0;
            $data['totalEvents'] = Event::count() ?: 0;
            $data['totalOrders'] = Order::count() ?: 0;
        }

        return view('profile.orders', $data);
    }
}