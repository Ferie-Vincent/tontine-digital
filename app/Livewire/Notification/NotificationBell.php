<?php

namespace App\Livewire\Notification;

use App\Models\Notification;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public $showDropdown = false;
    public $notifications = [];
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    #[On('notification-refresh')]
    #[On('contribution-declared')]
    #[On('contribution-confirmed')]
    #[On('tour-started')]
    #[On('tour-completed')]
    public function refreshNotifications()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $query = Notification::forUser(auth()->id());

        // Compter les non-lues et charger les 10 dernières en deux requêtes combinables
        $this->unreadCount = (clone $query)->unread()->count();
        $this->notifications = $query->latest()->take(10)->get();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === auth()->id()) {
            $notification->markAsRead();
        }
        $this->loadNotifications();
    }

    public function goToNotification($notificationId)
    {
        $notification = Notification::find($notificationId);
        if (!$notification || $notification->user_id !== auth()->id()) {
            return;
        }

        $notification->markAsRead();

        $data = $notification->data;
        if (isset($data['tontine_id']) && isset($data['tour_id'])) {
            return redirect()->route('tontines.tours.show', [$data['tontine_id'], $data['tour_id']]);
        } elseif (isset($data['tontine_id'])) {
            return redirect()->route('tontines.show', $data['tontine_id']);
        }

        return redirect()->route('notifications.index');
    }

    public function markAllAsRead()
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now(), 'status' => 'read']);

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification.notification-bell');
    }
}
