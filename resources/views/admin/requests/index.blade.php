<x-layouts.app :title="'Gestion des requêtes'">
    <x-slot:header>Gestion des requêtes</x-slot:header>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Requêtes des utilisateurs</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    {{ $pendingCount }} requête{{ $pendingCount > 1 ? 's' : '' }} en attente de traitement
                </p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap gap-4 mb-6">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.requests.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ !request('status') && !request('type') ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    Toutes
                </a>
                @foreach(\App\Enums\RequestStatus::cases() as $status)
                <a href="{{ route('admin.requests.index', ['status' => $status->value]) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === $status->value ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    {{ $status->label() }}
                </a>
                @endforeach
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach(\App\Enums\RequestType::cases() as $type)
                <a href="{{ route('admin.requests.index', ['type' => $type->value]) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ request('type') === $type->value ? 'bg-violet-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    {{ $type->label() }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Requests Table --}}
        @if($requests->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Aucune requête</h3>
            <p class="text-slate-500 dark:text-slate-400">Aucune requête ne correspond aux filtres sélectionnés.</p>
        </div>
        @else
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sujet</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($requests as $req)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $req->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $req->user->avatar_url }}" alt="{{ $req->user->name }}" class="w-8 h-8 rounded-full object-cover" />
                                    <div>
                                        <p class="font-medium text-slate-800 dark:text-white">{{ $req->user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $req->user->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-800 dark:text-white truncate max-w-[200px]">{{ $req->subject }}</p>
                                @if($req->tontine)
                                <p class="text-xs text-slate-400 mt-0.5">{{ $req->tontine->name }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4"><x-badge :color="$req->type->color()" size="xs">{{ $req->type->label() }}</x-badge></td>
                            <td class="px-6 py-4"><x-badge :color="$req->status->color()" size="xs" dot>{{ $req->status->label() }}</x-badge></td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs">{{ $req->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 text-right">
                                <x-button :href="route('admin.requests.show', $req)" variant="ghost" size="sm">Voir</x-button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $requests->withQueryString()->links() }}
        </div>
        @endif
    </div>
</x-layouts.app>
