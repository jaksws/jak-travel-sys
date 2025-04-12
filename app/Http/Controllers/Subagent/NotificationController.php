<?php

namespace App\Http\Controllers\Subagent;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications for subagent.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('subagent.notifications.index', compact('notifications'));
    }
    
    /**
     * Mark notification as read
     */
    public function markRead(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);
        
        $user = Auth::user();
        
        Notification::whereIn('id', $request->ids)
            ->where('user_id', $user->id)
            ->update(['read_at' => now()]);
            
        return response()->json([
            'success' => true
        ]);
    }
    
    /**
     * Remove the specified notification.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$notification) {
            return redirect()->route('subagent.notifications.index')
                ->with('error', 'الإشعار غير موجود');
        }
        
        $notification->delete();
        
        return redirect()->route('subagent.notifications.index')
            ->with('success', 'تم حذف الإشعار بنجاح');
    }
}