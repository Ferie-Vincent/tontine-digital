<x-layouts.app :title="'Mes requêtes'">
    <x-slot:header>Mes requêtes</x-slot:header>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Mes requêtes</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Suivez vos demandes et reclamations</p>
            </div>
            <x-button :href="route('requests.create')" variant="primary">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvelle requête
            </x-button>
        </div>

        {{-- Filters --}}
        <div class="mb-6">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('requests.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    Toutes
                </a>
                @foreach(\App\Enums\RequestStatus::cases() as $status)
                <a href="{{ route('requests.index', ['status' => $status->value]) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === $status->value ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    {{ $status->label() }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Requests List --}}
        @if($requests->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Aucune requête</h3>
            <p class="text-slate-500 dark:text-slate-400 mb-6">Vous n'avez soumis aucune requête pour le moment.</p>
            <x-button :href="route('requests.create')" variant="primary">Soumettre une requête</x-button>
        </div>
        @else
        <div class="space-y-4">
            @foreach($requests as $request)
            <a href="{{ route('requests.show', $request) }}" class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <x-badge :color="$request->type->color()" size="xs">{{ $request->type->label() }}</x-badge>
                            <x-badge :color="$request->status->color()" size="xs" dot>{{ $request->status->label() }}</x-badge>
                        </div>
                        <h3 class="text-base font-semibold text-slate-800 dark:text-white truncate">{{ $request->subject }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">{{ Str::limit($request->description, 120) }}</p>
                        <div class="flex items-center gap-4 mt-3 text-xs text-slate-400 dark:text-slate-500">
                            <span>{{ $request->created_at->diffForHumans() }}</span>
                            @if($request->tontine)
                            <span>{{ $request->tontine->name }}</span>
                            @endif
                        </div>
                    </div>
                    @if($request->admin_response)
                    <div class="shrink-0">
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Repondu
                        </span>
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $requests->withQueryString()->links() }}
        </div>
        @endif
    </div>
</x-layouts.app>
