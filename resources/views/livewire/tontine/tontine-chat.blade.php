<div wire:poll.5s="loadMessages"
     class="flex flex-col bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden"
     x-data="{
         shouldAutoScroll: true,
         scrollToBottom() {
             const el = this.$refs.chatMessages;
             if (el && this.shouldAutoScroll) {
                 el.scrollTop = el.scrollHeight;
             }
         },
         checkScroll() {
             const el = this.$refs.chatMessages;
             if (el) {
                 this.shouldAutoScroll = (el.scrollHeight - el.scrollTop - el.clientHeight) < 100;
             }
         }
     }"
     x-init="$nextTick(() => scrollToBottom())"
     @message-sent.window="$nextTick(() => { shouldAutoScroll = true; scrollToBottom(); })"
>
    {{-- Messages Area --}}
    <div x-ref="chatMessages"
         @scroll="checkScroll()"
         class="flex-1 overflow-y-auto p-4 space-y-3"
         style="height: 500px;">

        @if(empty($messages))
        <div class="flex flex-col items-center justify-center h-full text-center">
            <div class="w-16 h-16 mb-4 rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-medium">Aucun message</p>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Envoyez le premier message de la discussion</p>
        </div>
        @else
            @foreach($messages as $message)
                @if(!in_array($message['type'], ['text', 'image']))
                    {{-- System / Payment Submission Message --}}
                    <div class="flex justify-center">
                        <div class="max-w-md px-4 py-2 rounded-full
                            {{ $message['type'] === 'payment_submission'
                                ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20'
                                : 'bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400' }}
                            text-xs text-center">
                            @if($message['type'] === 'payment_submission')
                                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                            {{ $message['content'] }}
                            <span class="ml-2 opacity-60">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</span>
                        </div>
                    </div>
                @elseif(($message['user_id'] ?? null) === auth()->id())
                    {{-- Own Message --}}
                    @php
                        $avatarUrl = $message['user']['avatar'] ?? null;
                        $avatarUrl = $avatarUrl ? asset('storage/' . $avatarUrl) : \App\Models\User::generateAvatarSvg($message['user']['name'] ?? '?');
                    @endphp
                    <div class="flex justify-end gap-2.5">
                        <div class="max-w-[75%]">
                            <div class="px-4 py-2.5 rounded-2xl rounded-br-md bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm">
                                <p class="text-sm break-words">{{ $message['content'] }}</p>
                            </div>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 text-right mt-1 mr-1">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</p>
                        </div>
                        <img src="{{ $avatarUrl }}" alt="{{ $message['user']['name'] ?? '' }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 shadow-sm" />
                    </div>
                @else
                    {{-- Other's Message --}}
                    @php
                        $avatarUrl = $message['user']['avatar'] ?? null;
                        $avatarUrl = $avatarUrl ? asset('storage/' . $avatarUrl) : \App\Models\User::generateAvatarSvg($message['user']['name'] ?? '?');
                    @endphp
                    <div class="flex gap-2.5">
                        <img src="{{ $avatarUrl }}" alt="{{ $message['user']['name'] ?? '' }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 shadow-sm" />
                        <div class="max-w-[75%]">
                            <p class="text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">{{ $message['user']['name'] ?? 'Utilisateur' }}</p>
                            <div class="px-4 py-2.5 rounded-2xl rounded-bl-md bg-slate-100 dark:bg-slate-700 shadow-sm">
                                <p class="text-sm text-slate-800 dark:text-white break-words">{{ $message['content'] }}</p>
                            </div>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 ml-1">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    {{-- Input Area --}}
    <div class="border-t border-slate-200 dark:border-slate-700 p-3 bg-slate-50 dark:bg-slate-800/50">
        <form wire:submit="sendMessage" class="flex items-center gap-2">
            <input
                wire:model="newMessage"
                type="text"
                placeholder="Écrire un message..."
                class="flex-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                autocomplete="off"
                @keydown.enter.prevent="$wire.sendMessage()"
            />
            <button type="submit" class="p-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 shadow-md shadow-blue-500/30 transition-all disabled:opacity-50" wire:loading.attr="disabled">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </form>
    </div>
</div>
