<?php

namespace App\Livewire\Tontine;

use App\Models\Tontine;
use App\Models\TontineMessage;
use App\Models\TontineMessageRead;
use Livewire\Component;

class TontineChat extends Component
{
    public Tontine $tontine;
    public string $newMessage = '';
    public $messages = [];

    public function mount(Tontine $tontine)
    {
        $this->tontine = $tontine;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = TontineMessage::forTontine($this->tontine->id)
            ->with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values()
            ->toArray();

        $this->markAsRead();
    }

    private function markAsRead(): void
    {
        TontineMessageRead::updateOrCreate(
            ['user_id' => auth()->id(), 'tontine_id' => $this->tontine->id],
            ['last_read_at' => now()]
        );
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000',
        ]);

        TontineMessage::create([
            'tontine_id' => $this->tontine->id,
            'user_id' => auth()->id(),
            'type' => 'text',
            'content' => trim($this->newMessage),
        ]);

        $this->newMessage = '';
        $this->loadMessages();
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.tontine.tontine-chat');
    }
}
