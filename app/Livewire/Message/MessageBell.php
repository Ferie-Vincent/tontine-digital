<?php

namespace App\Livewire\Message;

use App\Models\TontineMessage;
use App\Models\TontineMessageRead;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageBell extends Component
{
    public int $unreadCount = 0;
    public $tontinesWithUnread = [];

    public function mount()
    {
        $this->loadUnread();
    }

    #[On('message-refresh')]
    #[On('message-sent')]
    public function refreshMessages()
    {
        $this->loadUnread();
    }

    public function loadUnread()
    {
        $userId = auth()->id();

        // Tontines de l'utilisateur (actif ou pending)
        $tontineIds = DB::table('tontine_members')
            ->where('user_id', $userId)
            ->whereIn('status', ['active', 'pending'])
            ->pluck('tontine_id');

        if ($tontineIds->isEmpty()) {
            $this->unreadCount = 0;
            $this->tontinesWithUnread = [];
            return;
        }

        // Dernieres lectures par tontine
        $reads = TontineMessageRead::where('user_id', $userId)
            ->whereIn('tontine_id', $tontineIds)
            ->pluck('last_read_at', 'tontine_id');

        // Compter les messages non lus par tontine en une seule requête
        $unreadCountsQuery = TontineMessage::whereIn('tontine_id', $tontineIds)
            ->where('user_id', '!=', $userId);

        // Construire les conditions de filtrage par date de lecture
        $unreadCountsQuery->where(function ($q) use ($reads, $tontineIds) {
            foreach ($tontineIds as $tontineId) {
                if (isset($reads[$tontineId])) {
                    $q->orWhere(function ($sub) use ($tontineId, $reads) {
                        $sub->where('tontine_id', $tontineId)
                            ->where('created_at', '>', $reads[$tontineId]);
                    });
                } else {
                    $q->orWhere('tontine_id', $tontineId);
                }
            }
        });

        $unreadCounts = $unreadCountsQuery
            ->selectRaw('tontine_id, count(*) as count')
            ->groupBy('tontine_id')
            ->pluck('count', 'tontine_id');

        if ($unreadCounts->isEmpty()) {
            $this->unreadCount = 0;
            $this->tontinesWithUnread = [];
            return;
        }

        // Charger les derniers messages par tontine en une seule requête (avec le user)
        $lastMessages = TontineMessage::whereIn('tontine_id', $unreadCounts->keys())
            ->where('user_id', '!=', $userId)
            ->whereIn('id', function ($sub) use ($unreadCounts, $userId) {
                $sub->selectRaw('MAX(id)')
                    ->from('tontine_messages')
                    ->whereIn('tontine_id', $unreadCounts->keys())
                    ->where('user_id', '!=', $userId)
                    ->groupBy('tontine_id');
            })
            ->with('user')
            ->get()
            ->keyBy('tontine_id');

        // Charger les noms des tontines en une seule requête
        $tontineNames = DB::table('tontines')
            ->whereIn('id', $unreadCounts->keys())
            ->pluck('name', 'id');

        $tontinesUnread = [];
        $totalUnread = 0;

        foreach ($unreadCounts as $tontineId => $count) {
            $lastMessage = $lastMessages[$tontineId] ?? null;
            $tontinesUnread[] = [
                'tontine_id' => $tontineId,
                'tontine_name' => $tontineNames[$tontineId] ?? '',
                'unread_count' => $count,
                'last_message' => $lastMessage?->content,
                'last_message_at' => $lastMessage?->created_at->diffForHumans(),
                'sender_name' => $lastMessage?->user?->name,
            ];
            $totalUnread += $count;
        }

        $this->unreadCount = $totalUnread;
        $this->tontinesWithUnread = $tontinesUnread;
    }

    public function goToTontineMessages($tontineId)
    {
        return redirect()->route('tontines.messages.index', $tontineId);
    }

    public function render()
    {
        return view('livewire.message.message-bell');
    }
}
