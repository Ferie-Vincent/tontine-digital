<x-layouts.app :title="'Modifier - ' . $tontine->name">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <a href="{{ route('tontines.show', $tontine) }}" class="text-text-muted hover:text-text transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-text">Modifier la tontine</h2>
        </div>
    </x-slot:header>

    <div class="max-w-2xl mx-auto">
        <x-card>
            <form method="POST" action="{{ route('tontines.update', $tontine) }}" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                @method('PUT')
                <x-input name="name" label="Nom de la tontine" :value="old('name', $tontine->name)" required />
                <x-textarea name="description" label="Description" :value="old('description', $tontine->description)" rows="3" />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input name="contribution_amount" label="Montant de cotisation (FCFA)" type="number" min="1000" step="500" :value="old('contribution_amount', $tontine->contribution_amount)" required />
                    <x-select name="frequency" label="Fréquence" required>
                        <option value="weekly" {{ old('frequency', $tontine->frequency->value) === 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="biweekly" {{ old('frequency', $tontine->frequency->value) === 'biweekly' ? 'selected' : '' }}>Bimensuelle</option>
                        <option value="monthly" {{ old('frequency', $tontine->frequency->value) === 'monthly' ? 'selected' : '' }}>Mensuelle</option>
                    </x-select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input name="target_amount_per_tour" label="Cagnotte visée par tour (FCFA)" type="number" min="1000" step="500" :value="old('target_amount_per_tour', $tontine->target_amount_per_tour)" hint="Montant indicatif que chaque beneficiaire devrait recevoir." />
                    <x-input name="target_amount_total" label="Objectif global (FCFA)" type="number" min="1000" step="500" :value="old('target_amount_total', $tontine->target_amount_total)" hint="Montant total visé sur toute la durée." />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input name="max_members" label="Nombre max de membres" type="number" min="2" max="100" :value="old('max_members', $tontine->max_members)" required />
                    <x-input name="end_date" label="Date de fin (optionnel)" type="date" :value="old('end_date', $tontine->end_date?->format('Y-m-d'))" />
                </div>
                <x-select name="status" label="Statut">
                    @foreach(\App\Enums\TontineStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ old('status', $tontine->status->value) === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                    @endforeach
                </x-select>
                <x-textarea name="rules" label="Règlement" :value="old('rules', $tontine->rules)" rows="4" />
                <div class="flex justify-between" x-data="{ showDeleteConfirm: false }">
                    <button type="button" @click="showDeleteConfirm = true" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                        Supprimer
                    </button>
                    <div class="flex gap-3">
                        <a href="{{ route('tontines.show', $tontine) }}"><x-button variant="ghost">Annuler</x-button></a>
                        <x-button type="submit" variant="primary" ::disabled="submitting">
                            <span x-show="!submitting">Enregistrer</span>
                            <span x-show="submitting" x-cloak class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Enregistrement...
                            </span>
                        </x-button>
                    </div>

                    {{-- Modale de confirmation de suppression --}}
                    <div x-show="showDeleteConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="fixed inset-0 bg-black/50" @click="showDeleteConfirm = false"></div>
                        <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Supprimer cette tontine ?</h3>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400 mb-6">Cette action est irréversible. Toutes les données de la tontine (tours, contributions, membres) seront supprimées.</p>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="showDeleteConfirm = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                    Annuler
                                </button>
                                <form method="POST" action="{{ route('tontines.destroy', $tontine) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </x-card>
        {{-- Paramètres avancés --}}
        @php
            $settings = array_merge(\App\Models\Tontine::defaultSettings(), $tontine->settings ?? []);
        @endphp
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800 dark:text-white">Paramètres avancés</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Retards, pénalités, rappels et exclusion automatique</p>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('tontines.settings.update', $tontine) }}" class="p-6 space-y-8" @submit="settingsSubmitting = true" x-data="{
                settingsSubmitting: false,
                lateEnabled: {{ $settings['late_detection_enabled'] ? 'true' : 'false' }},
                exclusionEnabled: {{ $settings['auto_exclusion_enabled'] ? 'true' : 'false' }},
                failureEnabled: {{ $settings['tour_failure_enabled'] ? 'true' : 'false' }},
                autoGenerateTours: {{ ($settings['auto_generate_tours'] ?? false) ? 'true' : 'false' }},
                autoStartTours: {{ ($settings['auto_start_tours'] ?? false) ? 'true' : 'false' }},
                autoStatusTransitions: {{ ($settings['auto_status_transitions'] ?? false) ? 'true' : 'false' }},
                autoDisburseReminder: {{ ($settings['auto_disburse_reminder'] ?? false) ? 'true' : 'false' }},
                collectionAlerts: {{ ($settings['collection_alerts_enabled'] ?? false) ? 'true' : 'false' }},
                autoReports: {{ ($settings['auto_reports_enabled'] ?? false) ? 'true' : 'false' }},
                reportSendToMembers: {{ ($settings['report_send_to_members'] ?? false) ? 'true' : 'false' }},
                autoReinstateEnabled: {{ ($settings['auto_reinstate_enabled'] ?? false) ? 'true' : 'false' }},
                autoRefundPenalty: {{ ($settings['auto_refund_penalty'] ?? false) ? 'true' : 'false' }},
                autoCloseTourEnabled: {{ ($settings['auto_close_tour_enabled'] ?? false) ? 'true' : 'false' }},
                penaltyEnabled: {{ ($settings['penalty_enabled'] ?? false) ? 'true' : 'false' }},
                penaltyType: '{{ $settings['penalty_type'] ?? 'fixed' }}'
            }">
                @csrf
                @method('PUT')

                {{-- Rappels automatiques --}}
                <div>
                    <h4 class="font-medium text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Rappels de cotisation
                    </h4>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jours de rappel avant échéance</label>
                        <input type="text" name="reminder_days_before" value="{{ implode(',', $settings['reminder_days_before'] ?? [3,1,0]) }}" placeholder="3,1,0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <p class="text-xs text-slate-400 mt-1">Séparez les jours par des virgules. Ex: 3,1,0 = rappels à J-3, J-1 et le jour J</p>
                    </div>
                </div>

                <hr class="border-slate-200 dark:border-slate-700">

                {{-- Détection de retard --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-medium text-slate-800 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Détection des retards
                        </h4>
                        <input type="hidden" name="late_detection_enabled" :value="lateEnabled ? '1' : '0'">
                        <button type="button" @click="lateEnabled = !lateEnabled"
                            :class="lateEnabled ? 'bg-amber-500' : 'bg-slate-300 dark:bg-slate-600'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                            <span :class="lateEnabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                        </button>
                    </div>
                    <div x-show="lateEnabled" x-collapse class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Délai avant retard (jours)</label>
                                <input type="number" name="late_threshold_days" value="{{ $settings['late_threshold_days'] }}" min="1" max="30" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <p class="text-xs text-slate-400 mt-1">Nombre de jours après l'échéance pour marquer en retard</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pénalité de retard (FCFA)</label>
                                <input type="number" name="late_penalty_amount" value="{{ $settings['late_penalty_amount'] }}" min="0" step="500" inputmode="numeric" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <p class="text-xs text-slate-400 mt-1">0 = pas de pénalité financière</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200 dark:border-slate-700">

                {{-- Exclusion automatique --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-medium text-slate-800 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            Exclusion automatique
                        </h4>
                        <input type="hidden" name="auto_exclusion_enabled" :value="exclusionEnabled ? '1' : '0'">
                        <button type="button" @click="exclusionEnabled = !exclusionEnabled"
                            :class="exclusionEnabled ? 'bg-red-500' : 'bg-slate-300 dark:bg-slate-600'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                            <span :class="exclusionEnabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                        </button>
                    </div>
                    <div x-show="exclusionEnabled" x-collapse>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Seuil de retards consécutifs</label>
                            <input type="number" name="auto_exclusion_threshold" value="{{ $settings['auto_exclusion_threshold'] }}" min="1" max="10" class="w-full sm:w-1/2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <p class="text-xs text-slate-400 mt-1">Le membre sera exclu après ce nombre de retards consécutifs</p>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200 dark:border-slate-700">

                {{-- Pénalités configurables --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-medium text-slate-800 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Pénalités configurables
                        </h4>
                        <input type="hidden" name="penalty_enabled" :value="penaltyEnabled ? '1' : '0'">
                        <button type="button" @click="penaltyEnabled = !penaltyEnabled"
                            :class="penaltyEnabled ? 'bg-rose-500' : 'bg-slate-300 dark:bg-slate-600'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                            <span :class="penaltyEnabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                        </button>
                    </div>
                    <div x-show="penaltyEnabled" x-collapse class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Type de pénalité</label>
                                <select name="penalty_type" x-model="penaltyType" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <option value="fixed">Montant fixe (FCFA)</option>
                                    <option value="percentage">Pourcentage (%)</option>
                                </select>
                                <p class="text-xs text-slate-400 mt-1">Choisir si la pénalité est un montant fixe ou un pourcentage de la cotisation</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    <span x-show="penaltyType === 'fixed'">Montant de la pénalité (FCFA)</span>
                                    <span x-show="penaltyType === 'percentage'">Pourcentage de pénalité (%)</span>
                                </label>
                                <input type="number" name="penalty_amount" value="{{ $settings['penalty_amount'] ?? 0 }}" min="0" :max="penaltyType === 'percentage' ? 100 : 1000000" :step="penaltyType === 'percentage' ? 1 : 500" inputmode="numeric" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <p class="text-xs text-slate-400 mt-1">
                                    <span x-show="penaltyType === 'fixed'">Montant fixe appliqué par contribution en retard. 0 = pas de pénalité</span>
                                    <span x-show="penaltyType === 'percentage'">Pourcentage du montant de cotisation. Ex: 10 = 10% de la cotisation</span>
                                </p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Délai de grâce avant pénalité (heures)</label>
                            <input type="number" name="penalty_grace_hours" value="{{ $settings['penalty_grace_hours'] ?? 24 }}" min="0" max="720" class="w-full sm:w-1/2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <p class="text-xs text-slate-400 mt-1">Nombre d'heures après l'échéance du tour avant d'appliquer la pénalité. 0 = immédiat</p>
                        </div>

                        {{-- Aperçu de la pénalité --}}
                        <div class="rounded-lg p-3 border border-rose-200 dark:border-rose-800/50" style="background-color: #fff1f2;">
                            <p class="text-sm font-medium text-rose-700 dark:text-rose-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Aperçu
                            </p>
                            <p class="text-xs text-rose-600 dark:text-rose-300 mt-1">
                                <span x-show="penaltyType === 'fixed'">
                                    Chaque membre en retard sera pénalisé de <strong x-text="new Intl.NumberFormat('fr-FR').format(document.querySelector('[name=penalty_amount]')?.value || 0) + ' FCFA'"></strong> après un délai de grâce de <strong x-text="(document.querySelector('[name=penalty_grace_hours]')?.value || 24) + 'h'"></strong>.
                                </span>
                                <span x-show="penaltyType === 'percentage'">
                                    Chaque membre en retard sera pénalisé de <strong x-text="(document.querySelector('[name=penalty_amount]')?.value || 0) + '%'"></strong> du montant de sa cotisation après un délai de grâce de <strong x-text="(document.querySelector('[name=penalty_grace_hours]')?.value || 24) + 'h'"></strong>.
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200 dark:border-slate-700">

                {{-- Échec de tour --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-medium text-slate-800 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            Échec automatique des tours
                        </h4>
                        <input type="hidden" name="tour_failure_enabled" :value="failureEnabled ? '1' : '0'">
                        <button type="button" @click="failureEnabled = !failureEnabled"
                            :class="failureEnabled ? 'bg-orange-500' : 'bg-slate-300 dark:bg-slate-600'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                            <span :class="failureEnabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                        </button>
                    </div>
                    <div x-show="failureEnabled" x-collapse class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Délai de grâce (jours)</label>
                                <input type="number" name="tour_failure_grace_days" value="{{ $settings['tour_failure_grace_days'] }}" min="1" max="30" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <p class="text-xs text-slate-400 mt-1">Jours après l'échéance avant de marquer le tour en échec</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Seuil minimum de collecte (%)</label>
                                <input type="number" name="tour_failure_min_collection_percent" value="{{ $settings['tour_failure_min_collection_percent'] }}" min="10" max="100" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <p class="text-xs text-slate-400 mt-1">Le tour échoue si la collecte est inférieure à ce pourcentage</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200 dark:border-slate-700">

                {{-- Automatisation du cycle de vie --}}
                <div>
                    <h4 class="font-medium text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Automatisation du cycle de vie
                    </h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Activez ces options pour que la tontine fonctionne de manière autonome.</p>

                    <div class="space-y-5">
                        {{-- Auto-transition des statuts --}}
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Transition automatique des statuts</p>
                                <p class="text-xs text-slate-400 mt-0.5">La tontine passe automatiquement de Brouillon &rarr; En attente &rarr; Active &rarr; Terminée selon les conditions remplies.</p>
                            </div>
                            <input type="hidden" name="auto_status_transitions" :value="autoStatusTransitions ? '1' : '0'">
                            <button type="button" @click="autoStatusTransitions = !autoStatusTransitions"
                                :class="autoStatusTransitions ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="autoStatusTransitions ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                            </button>
                        </div>
                        <div x-show="autoStatusTransitions" x-collapse>
                            <div class="ml-0 sm:ml-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre minimum de membres pour activer</label>
                                <input type="number" name="min_members_to_start" value="{{ $settings['min_members_to_start'] ?? 3 }}" min="2" max="100" class="w-full sm:w-1/2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <p class="text-xs text-slate-400 mt-1">La tontine passe en Active quand ce nombre de membres est atteint et la date de début est arrivée.</p>
                            </div>
                        </div>

                        {{-- Auto-génération des tours --}}
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Génération automatique des tours</p>
                                <p class="text-xs text-slate-400 mt-0.5">Les tours et contributions sont générés automatiquement dès que la tontine est active.</p>
                            </div>
                            <input type="hidden" name="auto_generate_tours" :value="autoGenerateTours ? '1' : '0'">
                            <button type="button" @click="autoGenerateTours = !autoGenerateTours"
                                :class="autoGenerateTours ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="autoGenerateTours ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                            </button>
                        </div>

                        {{-- Auto-démarrage des tours --}}
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Démarrage automatique des tours</p>
                                <p class="text-xs text-slate-400 mt-0.5">Chaque tour démarre automatiquement quand sa date approche, sans intervention de l'administrateur.</p>
                            </div>
                            <input type="hidden" name="auto_start_tours" :value="autoStartTours ? '1' : '0'">
                            <button type="button" @click="autoStartTours = !autoStartTours"
                                :class="autoStartTours ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="autoStartTours ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200 dark:border-slate-700">

                {{-- Sprint 2 — Automatisations avancées --}}
                <div>
                    <h4 class="font-medium text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Automatisations avancées
                    </h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Décaissement, alertes de collecte et rapports financiers automatiques.</p>

                    <div class="space-y-5">
                        {{-- Rappel de décaissement --}}
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Rappel de décaissement automatique</p>
                                <p class="text-xs text-slate-400 mt-0.5">Relance les managers quand un tour est prêt à être décaissé mais non encore versé.</p>
                            </div>
                            <input type="hidden" name="auto_disburse_reminder" :value="autoDisburseReminder ? '1' : '0'">
                            <button type="button" @click="autoDisburseReminder = !autoDisburseReminder"
                                :class="autoDisburseReminder ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-600'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="autoDisburseReminder ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                            </button>
                        </div>
                        <div x-show="autoDisburseReminder" x-collapse>
                            <div class="ml-0 sm:ml-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Délai avant relance (heures)</label>
                                <input type="number" name="disburse_reminder_delay_hours" value="{{ $settings['disburse_reminder_delay_hours'] ?? 24 }}" min="1" max="168" class="w-full sm:w-1/2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <p class="text-xs text-slate-400 mt-1">Après ce délai sans décaissement, les managers seront relancés (email + SMS si configuré).</p>
                            </div>
                        </div>

                        {{-- Alertes de collecte --}}
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Alertes proactives de collecte</p>
                                <p class="text-xs text-slate-400 mt-0.5">Alerte les managers quand la collecte est en danger avant l'échéance (seuils : 30% à J-5, 50% à J-3, 80% à J-1).</p>
                            </div>
                            <input type="hidden" name="collection_alerts_enabled" :value="collectionAlerts ? '1' : '0'">
                            <button type="button" @click="collectionAlerts = !collectionAlerts"
                                :class="collectionAlerts ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-600'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="collectionAlerts ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                            </button>
                        </div>

                        {{-- Rapports financiers --}}
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Rapports financiers automatiques</p>
                                <p class="text-xs text-slate-400 mt-0.5">Génère et envoie un rapport PDF périodique aux managers.</p>
                            </div>
                            <input type="hidden" name="auto_reports_enabled" :value="autoReports ? '1' : '0'">
                            <button type="button" @click="autoReports = !autoReports"
                                :class="autoReports ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-600'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="autoReports ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                            </button>
                        </div>
                        <div x-show="autoReports" x-collapse>
                            <div class="ml-0 sm:ml-4 space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Fréquence des rapports</label>
                                    <select name="report_frequency" class="w-full sm:w-1/2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        <option value="weekly" {{ ($settings['report_frequency'] ?? 'weekly') === 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                                        <option value="biweekly" {{ ($settings['report_frequency'] ?? '') === 'biweekly' ? 'selected' : '' }}>Bimensuel</option>
                                        <option value="monthly" {{ ($settings['report_frequency'] ?? '') === 'monthly' ? 'selected' : '' }}>Mensuel</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="hidden" name="report_send_to_members" :value="reportSendToMembers ? '1' : '0'">
                                    <button type="button" @click="reportSendToMembers = !reportSendToMembers"
                                        :class="reportSendToMembers ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-600'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                        <span :class="reportSendToMembers ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                                    </button>
                                    <span class="text-sm text-slate-700 dark:text-slate-300">Envoyer aussi aux membres (pas seulement aux managers)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200 dark:border-slate-700">

                {{-- Sprint 3 — Optimisations --}}
                <div>
                    <h4 class="font-medium text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Optimisations (Sprint 3)
                    </h4>

                    {{-- Auto-réintégration --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Réintégration automatique</p>
                                <p class="text-xs text-slate-400">Réintègre les membres exclus qui ont rattrapé tous leurs retards</p>
                            </div>
                            <div>
                                <input type="hidden" name="auto_reinstate_enabled" :value="autoReinstateEnabled ? 1 : 0">
                                <button type="button" @click="autoReinstateEnabled = !autoReinstateEnabled"
                                    :class="autoReinstateEnabled ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                    <span :class="autoReinstateEnabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                                </button>
                            </div>
                        </div>

                        <div x-show="autoReinstateEnabled" x-transition class="ml-4 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Délai de carence (jours)</label>
                                <input type="number" name="reinstate_grace_days" value="{{ $settings['reinstate_grace_days'] ?? 7 }}" min="1" max="30"
                                    class="w-32 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <p class="text-xs text-slate-400 mt-1">Nombre de jours après le dernier paiement confirmé avant de réintégrer</p>
                            </div>
                        </div>

                        <hr class="border-slate-100 dark:border-slate-700/50">

                        {{-- Auto-remboursement de pénalité --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Remboursement automatique des pénalités</p>
                                <p class="text-xs text-slate-400">Annule la pénalité lorsqu'un paiement en retard est finalement confirmé</p>
                            </div>
                            <div>
                                <input type="hidden" name="auto_refund_penalty" :value="autoRefundPenalty ? 1 : 0">
                                <button type="button" @click="autoRefundPenalty = !autoRefundPenalty"
                                    :class="autoRefundPenalty ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                    <span :class="autoRefundPenalty ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200 dark:border-slate-700">

                {{-- Sprint 5 — Clôture automatique des tours --}}
                <div>
                    <h4 class="font-medium text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Clôture automatique des tours (Sprint 5)
                    </h4>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Auto-confirmation de réception</p>
                                <p class="text-xs text-slate-400">Confirme automatiquement la réception des fonds si le bénéficiaire ne répond pas dans le délai imparti</p>
                            </div>
                            <div>
                                <input type="hidden" name="auto_close_tour_enabled" :value="autoCloseTourEnabled ? 1 : 0">
                                <button type="button" @click="autoCloseTourEnabled = !autoCloseTourEnabled"
                                    :class="autoCloseTourEnabled ? 'bg-violet-500' : 'bg-slate-300 dark:bg-slate-600'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none">
                                    <span :class="autoCloseTourEnabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                                </button>
                            </div>
                        </div>

                        <div x-show="autoCloseTourEnabled" x-transition class="ml-4 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Délai avant clôture automatique (jours)</label>
                                <input type="number" name="auto_close_tour_days" value="{{ $settings['auto_close_tour_days'] ?? 7 }}" min="3" max="30"
                                    class="w-32 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500">
                                <p class="text-xs text-slate-400 mt-1">Nombre de jours après le décaissement avant de confirmer automatiquement la réception (min. 3, max. 30)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-700">
                    <x-button type="submit" variant="primary" ::disabled="settingsSubmitting">
                        <span x-show="!settingsSubmitting">Enregistrer les paramètres</span>
                        <span x-show="settingsSubmitting" x-cloak class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Enregistrement...
                        </span>
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
