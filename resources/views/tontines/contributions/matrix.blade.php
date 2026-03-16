<x-layouts.app :title="'Matrice - ' . $tontine->name">
    <x-slot:header>
        <div class="flex items-center gap-4">
            <a href="{{ route('tontines.show', $tontine) }}"
               class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Matrice des contributions</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $tontine->name }}</p>
            </div>
        </div>
    </x-slot:header>

    {{-- Export button --}}
    @if(auth()->user()->canManage($tontine))
    <div class="flex gap-2 mb-4">
        <a href="{{ route('tontines.contributions.matrix.export.csv', ['tontine' => $tontine->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Exporter CSV
        </a>
    </div>
    @endif

    {{-- Vue mobile : cards --}}
    <div class="lg:hidden space-y-4">
        @foreach($members as $member)
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center gap-3 mb-3">
                <x-avatar :user="$member->user" size="sm" />
                <div>
                    <p class="font-medium text-slate-900 dark:text-white">{{ $member->user->name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $member->parts }} part(s)</p>
                </div>
            </div>
            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                @foreach($tours as $tour)
                    @php
                        $contrib = $contributions->get($tour->id)?->get($member->user_id);
                        $status = $contrib?->status?->value ?? null;
                        $isBeneficiary = $tour->beneficiary_id === $member->user_id;

                        $cellBg = match($status) {
                            'confirmed' => 'bg-emerald-50 dark:bg-emerald-900/20',
                            'declared' => 'bg-amber-50 dark:bg-amber-900/20',
                            'late' => 'bg-orange-50 dark:bg-orange-900/20',
                            'rejected' => 'bg-red-50 dark:bg-red-900/20',
                            'pending' => 'bg-slate-50 dark:bg-slate-700/50',
                            default => $isBeneficiary ? 'bg-sky-50 dark:bg-sky-900/20' : 'bg-slate-50 dark:bg-slate-700/30',
                        };

                        $cellText = match($status) {
                            'confirmed' => 'text-emerald-600 dark:text-emerald-400',
                            'declared' => 'text-amber-600 dark:text-amber-400',
                            'late' => 'text-orange-600 dark:text-orange-400',
                            'rejected' => 'text-red-600 dark:text-red-400',
                            'pending' => 'text-slate-500 dark:text-slate-400',
                            default => $isBeneficiary ? 'text-sky-600 dark:text-sky-400' : 'text-slate-300 dark:text-slate-600',
                        };

                        $cellIcon = match($status) {
                            'confirmed' => '✓',
                            'declared' => '⏳',
                            'late' => '!',
                            'rejected' => '✗',
                            'pending' => '—',
                            default => $isBeneficiary ? '★' : '',
                        };
                    @endphp
                    <div class="text-center p-2 rounded-lg {{ $cellBg }}">
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">T{{ $tour->tour_number }}</p>
                        <p class="text-xs font-medium {{ $cellText }}">{{ $cellIcon }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        {{-- Légende mobile --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-3">Légende :</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold">✓</span>
                    <span class="text-xs text-slate-600 dark:text-slate-300">Confirmé</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs">⏳</span>
                    <span class="text-xs text-slate-600 dark:text-slate-300">Déclaré</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 text-xs font-bold">✗</span>
                    <span class="text-xs text-slate-600 dark:text-slate-300">Rejeté</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 text-xs font-bold">!</span>
                    <span class="text-xs text-slate-600 dark:text-slate-300">En retard</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-slate-100 dark:bg-slate-600/30 text-slate-500 dark:text-slate-400 text-xs">—</span>
                    <span class="text-xs text-slate-600 dark:text-slate-300">En attente</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-sky-100 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 text-xs">★</span>
                    <span class="text-xs text-slate-600 dark:text-slate-300">Bénéficiaire</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Vue desktop : tableau --}}
    <div class="hidden lg:block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-700/50">
                        <th class="sticky left-0 z-20 bg-slate-50 dark:bg-slate-700 px-4 py-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider border-b border-slate-200 dark:border-slate-600">
                            Membre
                        </th>
                        @foreach($tours as $tour)
                        <th class="px-3 py-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-600">
                            <div class="uppercase tracking-wider">T{{ $tour->tour_number }}</div>
                            <div class="text-[10px] font-normal text-slate-400 dark:text-slate-500 mt-0.5">{{ $tour->due_date->format('d/m') }}</div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach($members as $member)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors duration-150">
                        <td class="sticky left-0 z-10 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/30 px-4 py-3 border-r border-slate-100 dark:border-slate-700/50 transition-colors duration-150">
                            <div class="flex items-center gap-3">
                                <x-avatar :user="$member->user" size="xs" />
                                <span class="text-slate-700 dark:text-slate-200 text-xs font-medium whitespace-nowrap">{{ $member->user->name }}</span>
                            </div>
                        </td>
                        @foreach($tours as $tour)
                        @php
                            $contrib = $contributions->get($tour->id)?->get($member->user_id);
                            $statusConfig = match($contrib?->status?->value ?? null) {
                                'confirmed' => [
                                    'bg' => 'bg-emerald-100 dark:bg-emerald-500/20',
                                    'text' => 'text-emerald-600 dark:text-emerald-400',
                                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>'
                                ],
                                'declared' => [
                                    'bg' => 'bg-amber-100 dark:bg-amber-500/20',
                                    'text' => 'text-amber-600 dark:text-amber-400',
                                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                ],
                                'rejected' => [
                                    'bg' => 'bg-red-100 dark:bg-red-500/20',
                                    'text' => 'text-red-600 dark:text-red-400',
                                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>'
                                ],
                                'late' => [
                                    'bg' => 'bg-orange-100 dark:bg-orange-500/20',
                                    'text' => 'text-orange-600 dark:text-orange-400',
                                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
                                ],
                                'pending' => [
                                    'bg' => 'bg-slate-100 dark:bg-slate-600/30',
                                    'text' => 'text-slate-500 dark:text-slate-400',
                                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>'
                                ],
                                default => ($tour->beneficiary_id === $member->user_id) ? [
                                    'bg' => 'bg-sky-100 dark:bg-sky-500/20',
                                    'text' => 'text-sky-600 dark:text-sky-400',
                                    'icon' => '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>'
                                ] : [
                                    'bg' => 'bg-slate-50 dark:bg-slate-700/20',
                                    'text' => 'text-slate-300 dark:text-slate-600',
                                    'icon' => ''
                                ],
                            };
                        @endphp
                        <td class="px-3 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} transition-transform duration-150 hover:scale-110">
                                {!! $statusConfig['icon'] !!}
                            </span>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Legend -->
        <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 mr-2">Legende:</span>

                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Confirme
                </span>

                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Declare
                </span>

                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    Rejete
                </span>

                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    En retard
                </span>

                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-600/30 text-slate-600 dark:text-slate-400 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    En attente
                </span>

                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-400 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    Bénéficiaire
                </span>
            </div>
        </div>
    </div>
</x-layouts.app>
