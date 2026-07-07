<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function index(Request $request): View
    {
        $notifications = $this->notificationService->getUserNotifications(
            $request->user()->id,
            $request->user()->company_id ?? 1,
            $request->only(['search', 'status', 'type', 'priority']),
        );

        $unreadCount = $this->notificationService->getUnreadCount($request->user()->id);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function show(UserNotification $notification): View
    {
        if ($notification->isUnread()) {
            $this->notificationService->markAsRead($notification);
        }

        return view('notifications.show', ['notification' => $notification]);
    }

    public function markAsRead(UserNotification $notification): RedirectResponse
    {
        $this->notificationService->markAsRead($notification);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $this->notificationService->markAllAsRead(
            $request->user()->id,
            $request->user()->company_id ?? 1,
        );

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(UserNotification $notification): RedirectResponse
    {
        $this->notificationService->delete($notification);

        return back()->with('success', 'Notification deleted.');
    }

    public function archive(UserNotification $notification): RedirectResponse
    {
        $this->notificationService->archive($notification);

        return back()->with('success', 'Notification archived.');
    }

    public function unreadCount(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'count' => $this->notificationService->getUnreadCount($request->user()->id),
        ]);
    }
}
