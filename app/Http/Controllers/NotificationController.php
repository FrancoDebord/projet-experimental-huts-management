<?php

namespace App\Http\Controllers;

use App\Models\HutNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        // Fire any pending start/end notifications
        NotificationService::checkAndFirePendingNotifications();

        $notifications = HutNotification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        HutNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    /** GET /notifications/unread-count — AJAX */
    public function unreadCount()
    {
        $count = HutNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /** GET /notifications/latest — AJAX for dropdown */
    public function latest()
    {
        $notifications = HutNotification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return response()->json($notifications->map(fn($n) => [
            'id'         => $n->id,
            'title'      => $n->title,
            'message'    => \Str::limit($n->message, 80),
            'type'       => $n->type,
            'icon'       => $n->icon,
            'url'        => $n->url,
            'read'       => $n->is_read,
            'created_at' => $n->created_at->diffForHumans(),
        ]));
    }

    /** POST /notifications/{id}/read */
    public function markRead(int $id)
    {
        HutNotification::where('user_id', Auth::id())->findOrFail($id)->markAsRead();
        return response()->json(['ok' => true]);
    }

    /** POST /notifications/read-all */
    public function markAllRead()
    {
        HutNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Toutes les notifications marquées comme lues.');
    }

    /** POST /notifications/push-subscribe */
    public function pushSubscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
            'p256dh'   => 'nullable|string',
            'auth'     => 'nullable|string',
        ]);

        \App\Models\UserPref::forUser(Auth::id())->update(['push_enabled' => true]);

        // Store subscription
        \DB::table('exp_huts_push_subscriptions')->updateOrInsert(
            ['user_id' => Auth::id(), 'endpoint' => $validated['endpoint']],
            ['p256dh'  => $validated['p256dh'], 'auth' => $validated['auth'],
             'user_agent' => $request->userAgent(), 'updated_at' => now(), 'created_at' => now()]
        );

        return response()->json(['ok' => true]);
    }

    /** POST /notifications/push-unsubscribe */
    public function pushUnsubscribe(Request $request)
    {
        \App\Models\UserPref::forUser(Auth::id())->update(['push_enabled' => false]);
        \DB::table('exp_huts_push_subscriptions')->where('user_id', Auth::id())->delete();
        return response()->json(['ok' => true]);
    }
}
