<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(15);
        
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        
        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * عرض الإشعارات غير المقروءة كجزء من dropdown
     */
    public function unread()
    {
        $notifications = auth()->user()->notifications()->unread()->latest()->take(5)->get();
        return response()->json([
            'count' => auth()->user()->notifications()->unread()->count(),
            'notifications' => $notifications
        ]);
    }
    
    /**
     * تعليم جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        auth()->user()->notifications()->unread()->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
}
