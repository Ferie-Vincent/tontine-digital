<x-layouts.app title="Mode d'emploi">
    <x-slot:header>Mode d'emploi</x-slot:header>

    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-2xl p-8 sm:p-12 mb-8" style="background: linear-gradient(135deg, #3C50E0, #1C3FB7);">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-white/60 text-sm font-medium uppercase tracking-wider">Documentation</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Mode d'emploi</h1>
            <p class="text-lg text-white/80 max-w-2xl">Guide complet de la plateforme DIGI-TONTINE CI. Tout ce que vous devez savoir pour administrer et utiliser la plateforme de A a Z.</p>
        </div>
    </div>

    <div class="lg:flex lg:gap-8" x-data="guideNav()">
        {{-- Table des matieres - Sidebar desktop / Barre mobile --}}
        <aside class="lg:w-72 flex-shrink-0 mb-6 lg:mb-0">
            <div class="lg:sticky lg:top-24 lg:max-h-[calc(100vh-8rem)] lg:overflow-y-auto">
                {{-- Mobile: barre horizontale scrollable --}}
                <div class="lg:hidden overflow-x-auto pb-2 -mx-4 px-4">
                    <div class="flex gap-2 min-w-max">
                        <template x-for="item in sections" :key="item.id">
                            <a :href="'#' + item.id" @click.prevent="scrollTo(item.id)"
                               class="px-3 py-2 text-xs font-medium rounded-full whitespace-nowrap border transition-colors"
                               :class="active === item.id ? 'bg-[#3C50E0] text-white border-[#3C50E0]' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700'">
                                <span x-text="item.short"></span>
                            </a>
                        </template>
                    </div>
                </div>
                {{-- Desktop: sidebar --}}
                <nav class="hidden lg:block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 px-3">Sommaire</p>
                    <template x-for="item in sections" :key="item.id">
                        <a :href="'#' + item.id" @click.prevent="scrollTo(item.id)"
                           class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors mb-0.5"
                           :class="active === item.id ? 'bg-[#3C50E0]/10 text-[#3C50E0] font-medium' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                            <span class="w-5 h-5 rounded-full text-[10px] font-bold flex items-center justify-center flex-shrink-0"
                                  :class="active === item.id ? 'bg-[#3C50E0] text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'"
                                  x-text="item.num"></span>
                            <span x-text="item.label" class="truncate"></span>
                        </a>
                    </template>
                </nav>
            </div>
        </aside>

        {{-- Contenu principal --}}
        <div class="flex-1 min-w-0 space-y-12">

            {{-- ============================================================ --}}
            {{-- SECTION 1 : Introduction --}}
            {{-- ============================================================ --}}
            <section id="introduction" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-[#3C50E0]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#3C50E0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Introduction</h2>
                    </div>

                    <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                        DIGI-TONTINE CI est une plateforme de gestion de tontines numeriques concue pour la Cote d'Ivoire.
                        Elle permet de creer et gerer des groupes d'epargne rotative (tontines) de maniere transparente et securisee.
                        Ce guide vous explique pas a pas toutes les fonctionnalites de la plateforme.
                    </p>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Fonctionnalites principales</h3>
                    <div class="grid sm:grid-cols-2 gap-3 mb-6">
                        @php
                        $features = [
                            ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Creation et gestion de tontines (hebdomadaire, bimensuel, mensuel)'],
                            ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label' => 'Suivi en temps reel des contributions de chaque membre'],
                            ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'label' => 'Systeme de tours automatise avec beneficiaires rotatifs'],
                            ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Preuves de paiement (Orange Money, MTN, Wave, virement, especes)'],
                            ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'label' => 'Notifications multi-canal (app, email, push)'],
                            ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'label' => 'Chat integre par tontine'],
                            ['icon' => 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Exports PDF et CSV'],
                            ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'label' => 'Panneau d\'administration complet'],
                        ];
                        @endphp
                        @foreach($features as $f)
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                            <svg class="w-5 h-5 text-[#3C50E0] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/></svg>
                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $f['label'] }}</span>
                        </div>
                        @endforeach
                    </div>

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Page d\'accueil - Vue d\'ensemble de la plateforme']) --}}
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 2 : Concepts de base --}}
            {{-- ============================================================ --}}
            <section id="concepts" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Concepts de base</h2>
                    </div>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                        <div class="p-5 rounded-xl border-2 border-[#3C50E0]/20 bg-[#3C50E0]/5 dark:bg-[#3C50E0]/10">
                            <h4 class="font-bold text-[#3C50E0] mb-2">Tontine</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Un groupe d'epargne rotative. Chaque membre contribue un montant fixe a intervalles reguliers. A chaque tour, un membre recoit la totalite de la cagnotte.</p>
                        </div>
                        <div class="p-5 rounded-xl border-2 border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20">
                            <h4 class="font-bold text-emerald-600 dark:text-emerald-400 mb-2">Membre</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Un participant inscrit dans une tontine. Roles possibles : <strong>ADMIN</strong> (createur), <strong>TRESORIER</strong> (gere les finances) ou <strong>MEMBRE</strong> (participant).</p>
                        </div>
                        <div class="p-5 rounded-xl border-2 border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20">
                            <h4 class="font-bold text-amber-600 dark:text-amber-400 mb-2">Tour</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Un cycle de collecte. Chaque tour a un beneficiaire designe qui recoit les fonds collectes. Generes automatiquement selon l'ordre des positions.</p>
                        </div>
                        <div class="p-5 rounded-xl border-2 border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20">
                            <h4 class="font-bold text-blue-600 dark:text-blue-400 mb-2">Contribution</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Le paiement d'un membre pour un tour. Statuts : EN ATTENTE, DECLAREE, CONFIRMEE, REJETEE ou EN RETARD.</p>
                        </div>
                        <div class="p-5 rounded-xl border-2 border-purple-200 dark:border-purple-800 bg-purple-50 dark:bg-purple-900/20">
                            <h4 class="font-bold text-purple-600 dark:text-purple-400 mb-2">Parts</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Multiplicateur de contribution. Un membre avec 2 parts contribue le double et recoit le double quand c'est son tour.</p>
                        </div>
                        <div class="p-5 rounded-xl border-2 border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20">
                            <h4 class="font-bold text-rose-600 dark:text-rose-400 mb-2">Preuve de paiement</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Justificatif (capture d'ecran, reference) attestant un paiement via Orange Money, MTN MoMo, Wave, especes ou virement.</p>
                        </div>
                    </div>

                    {{-- Flux principal --}}
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Flux principal</h3>
                    <div class="flex flex-wrap items-center gap-2 mb-8 p-4 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                        @php
                        $flow = [
                            ['label' => 'Utilisateur', 'color' => 'bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200'],
                            ['label' => 'Rejoint / Cree Tontine', 'color' => 'bg-[#3C50E0]/10 text-[#3C50E0]'],
                            ['label' => 'Tours generes', 'color' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'],
                            ['label' => 'Contributions collectees', 'color' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'],
                            ['label' => 'Beneficiaire recoit la cagnotte', 'color' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'],
                        ];
                        @endphp
                        @foreach($flow as $i => $step)
                        <span class="px-4 py-2 rounded-lg text-sm font-medium {{ $step['color'] }}">{{ $step['label'] }}</span>
                        @if(!$loop->last)
                        <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        @endif
                        @endforeach
                    </div>

                    {{-- Cycle de vie d'une contribution --}}
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Cycle de vie d'une contribution</h3>
                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                        <div class="flex flex-wrap items-center justify-center gap-3">
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300">EN ATTENTE</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400">DECLAREE</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            <div class="flex flex-col gap-2">
                                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400">CONFIRMEE</span>
                                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400">REJETEE</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center gap-3 mt-3">
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300">EN ATTENTE</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            <span class="text-xs text-slate-400 italic">delai depasse</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400">EN RETARD</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 3 : Inscription et Connexion --}}
            {{-- ============================================================ --}}
            <section id="auth" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Inscription et Connexion</h2>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Inscription</h3>

                    @include('admin.guide._step', ['num' => 1, 'title' => 'Accedez a la page d\'inscription', 'desc' => 'Depuis la page de connexion, cliquez sur le lien "Creer un compte".'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Remplissez le formulaire', 'desc' => 'Nom complet, numero de telephone (format CI : +225), email (optionnel), mot de passe.'])
                    @include('admin.guide._step', ['num' => 3, 'title' => 'Verifiez votre numero', 'desc' => 'Un code OTP est envoye par SMS. Saisissez-le pour valider votre numero de telephone.'])
                    @include('admin.guide._step', ['num' => 4, 'title' => 'Acces au tableau de bord', 'desc' => 'Une fois inscrit, vous etes redirige vers votre tableau de bord personnel.'])

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Page d\'inscription avec formulaire']) --}}

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4 mt-8">Connexion</h3>

                    @include('admin.guide._step', ['num' => 1, 'title' => 'Identifiez-vous', 'desc' => 'Entrez votre numero de telephone et votre mot de passe.'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Mot de passe oublie', 'desc' => 'Utilisez le lien "Mot de passe oublie" pour reinitialiser par email ou SMS.'])

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Page de connexion']) --}}

                    @include('admin.guide._info', ['text' => 'Le compte est verrouille apres plusieurs tentatives echouees. Un administrateur peut le deverrouiller depuis le panneau d\'administration.'])
                    @include('admin.guide._warning', ['text' => 'Si un administrateur vous a cree un compte, vous devrez changer votre mot de passe temporaire a la premiere connexion.'])
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 4 : Tableau de bord utilisateur --}}
            {{-- ============================================================ --}}
            <section id="dashboard" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Tableau de bord utilisateur</h2>
                    </div>

                    <p class="text-slate-600 dark:text-slate-400 mb-6">Le tableau de bord est la page d'accueil apres connexion. Il donne une vue d'ensemble de votre activite.</p>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Alertes d'action</h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-4">Des cartes colorees en haut selon l'urgence :</p>
                    <div class="grid sm:grid-cols-2 gap-3 mb-6">
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                            <div class="w-3 h-3 rounded-full bg-red-500 flex-shrink-0"></div>
                            <span class="text-sm text-red-700 dark:text-red-400"><strong>Critique</strong> : Paiements en retard</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                            <div class="w-3 h-3 rounded-full bg-amber-500 flex-shrink-0"></div>
                            <span class="text-sm text-amber-700 dark:text-amber-400"><strong>Attention</strong> : Paiements a valider, fonds a decaisser</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                            <div class="w-3 h-3 rounded-full bg-blue-500 flex-shrink-0"></div>
                            <span class="text-sm text-blue-700 dark:text-blue-400"><strong>Info</strong> : Demandes d'adhesion en attente</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
                            <div class="w-3 h-3 rounded-full bg-emerald-500 flex-shrink-0"></div>
                            <span class="text-sm text-emerald-700 dark:text-emerald-400"><strong>Succes</strong> : Vous etes beneficiaire d'un tour</span>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Statistiques rapides</h3>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                        @foreach(['Tontines actives', 'Contributions du mois', 'Total contributions', 'Taux de contribution'] as $stat)
                        <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/30 text-center">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">{{ $stat }}</p>
                            <p class="text-lg font-bold text-slate-800 dark:text-white">--</p>
                        </div>
                        @endforeach
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Graphiques</h3>
                    <ul class="list-disc list-inside text-slate-600 dark:text-slate-400 space-y-1 mb-6">
                        <li>Evolution des contributions sur 6 mois</li>
                        <li>Repartition par statut de tontine</li>
                        <li>Repartition par tontine</li>
                    </ul>

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Tableau de bord utilisateur avec alertes et statistiques']) --}}
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 5 : Creer une tontine --}}
            {{-- ============================================================ --}}
            <section id="creer-tontine" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Creer une tontine</h2>
                    </div>

                    @include('admin.guide._step', ['num' => 1, 'title' => 'Accedez au formulaire', 'desc' => 'Menu "Mes Tontines" puis cliquez sur "Creer une tontine".'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Remplissez les informations', 'desc' => 'Nom, description (optionnelle), montant de la contribution en FCFA, frequence (hebdomadaire, bimensuel, mensuel), date de debut, nombre max de membres.'])
                    @include('admin.guide._step', ['num' => 3, 'title' => 'Tontine creee', 'desc' => 'La tontine est creee avec le statut "EN ATTENTE". Vous en etes automatiquement l\'administrateur.'])
                    @include('admin.guide._step', ['num' => 4, 'title' => 'Code d\'invitation', 'desc' => 'Un code unique est genere (ex: TON-XXXXXX). Partagez-le ou utilisez le QR code pour inviter des membres.'])

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Formulaire de creation d\'une tontine']) --}}

                    @include('admin.guide._info', ['text' => 'Vous pouvez dupliquer une tontine existante pour en recreer une avec les memes parametres via le bouton "Dupliquer".'])

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3 mt-6">Parametres avances</h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-3">Accessibles depuis l'onglet Parametres dans la page de la tontine :</p>
                    <ul class="list-disc list-inside text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                        <li>Detection automatique des retards (nombre de jours)</li>
                        <li>Penalites de retard (montant en FCFA)</li>
                        <li>Exclusion automatique apres X retards</li>
                        <li>Rappels avant echeance</li>
                        <li>Delai de grace pour les tours echoues</li>
                    </ul>

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Page detail d\'une tontine avec code d\'invitation et QR code']) --}}
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 6 : Gerer les membres --}}
            {{-- ============================================================ --}}
            <section id="membres" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Gerer les membres</h2>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Ajouter des membres</h3>
                    @include('admin.guide._step', ['num' => 1, 'title' => 'Page des membres', 'desc' => 'Depuis la page de la tontine, cliquez sur "Membres".'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Plusieurs methodes', 'desc' => 'Rechercher un utilisateur existant, creer un compte et l\'ajouter, partager le code d\'invitation, importer une liste CSV, ou envoyer une invitation par email/SMS.'])

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Page de gestion des membres avec actions']) --}}

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3 mt-6">Roles disponibles</h3>
                    <div class="grid sm:grid-cols-3 gap-3 mb-6">
                        <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                            <h4 class="font-bold text-red-700 dark:text-red-400 mb-1">ADMIN</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-400">Controle total sur la tontine. Attribue automatiquement au createur.</p>
                        </div>
                        <div class="p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                            <h4 class="font-bold text-amber-700 dark:text-amber-400 mb-1">TRESORIER</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-400">Peut confirmer/rejeter les contributions et decaisser les fonds.</p>
                        </div>
                        <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                            <h4 class="font-bold text-blue-700 dark:text-blue-400 mb-1">MEMBRE</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-400">Declare ses contributions, participe au chat.</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Actions sur un membre</h3>
                    <ul class="list-disc list-inside text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                        <li>Changer le role</li>
                        <li>Modifier le nombre de parts</li>
                        <li>Reorganiser les positions (drag & drop ou saisie manuelle)</li>
                        <li>Accepter / Rejeter une demande d'adhesion</li>
                        <li>Exclure un membre</li>
                        <li>Voir la performance (taux de paiement, retards, fiabilite)</li>
                    </ul>

                    @include('admin.guide._info', ['text' => 'Les administrateurs systeme (super admin) peuvent acceder a toutes les tontines sans en etre membres.'])
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 7 : Les tours --}}
            {{-- ============================================================ --}}
            <section id="tours" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Les tours (cycles)</h2>
                    </div>

                    <p class="text-slate-600 dark:text-slate-400 mb-6">Les tours representent les cycles de collecte. Chaque tour a un beneficiaire qui recevra la cagnotte.</p>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Generation des tours</h3>
                    @include('admin.guide._step', ['num' => 1, 'title' => 'Generation automatique', 'desc' => 'Les tours sont generes automatiquement selon l\'ordre des positions des membres.'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Round-robin', 'desc' => 'Chaque membre beneficie d\'un tour dans l\'ordre de sa position.'])
                    @include('admin.guide._step', ['num' => 3, 'title' => 'Parts multiples', 'desc' => 'Un membre avec 2 parts aura 2 tours (il recoit 2 fois la cagnotte au total).'])

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4 mt-6">Cycle de vie d'un tour</h3>
                    <div class="grid sm:grid-cols-5 gap-3 mb-6">
                        @php
                        $tourStatuses = [
                            ['label' => 'EN ATTENTE', 'desc' => 'Tour planifie', 'color' => 'bg-slate-100 dark:bg-slate-700 border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300'],
                            ['label' => 'EN COURS', 'desc' => 'Collecte en cours', 'color' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-400'],
                            ['label' => 'COMPLETE', 'desc' => 'Contributions confirmees', 'color' => 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-300 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400'],
                            ['label' => 'DECAISSE', 'desc' => 'Fonds verses', 'color' => 'bg-purple-50 dark:bg-purple-900/20 border-purple-300 dark:border-purple-700 text-purple-700 dark:text-purple-400'],
                            ['label' => 'ECHOUE', 'desc' => 'Relance possible', 'color' => 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700 text-red-700 dark:text-red-400'],
                        ];
                        @endphp
                        @foreach($tourStatuses as $ts)
                        <div class="p-3 rounded-lg border {{ $ts['color'] }} text-center">
                            <p class="text-xs font-bold mb-1">{{ $ts['label'] }}</p>
                            <p class="text-[10px] opacity-75">{{ $ts['desc'] }}</p>
                        </div>
                        @endforeach
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Actions admin / tresorier</h3>
                    <ul class="list-disc list-inside text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                        <li>Demarrer un tour</li>
                        <li>Completer un tour manuellement</li>
                        <li>Decaisser les fonds au beneficiaire</li>
                        <li>Confirmer la reception par le beneficiaire</li>
                        <li>Reassigner le beneficiaire d'un tour</li>
                        <li>Relancer un tour echoue</li>
                    </ul>

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Liste des tours avec statistiques et actions']) --}}
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 8 : Les contributions --}}
            {{-- ============================================================ --}}
            <section id="contributions" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Les contributions</h2>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 mb-6">
                        <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                            <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Vue liste</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Toutes les contributions avec filtres par tour et par statut.</p>
                        </div>
                        <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                            <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Vue matrice</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Tableau croise membres x tours pour une vue d'ensemble complete.</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Workflow de validation</h3>
                    @include('admin.guide._step', ['num' => 1, 'title' => 'Declaration', 'desc' => 'Le membre declare sa contribution avec preuve de paiement.'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Notification', 'desc' => 'L\'admin ou le tresorier recoit une notification.'])
                    @include('admin.guide._step', ['num' => 3, 'title' => 'Verification', 'desc' => 'Il verifie la preuve de paiement et confirme ou rejette.'])
                    @include('admin.guide._step', ['num' => 4, 'title' => 'Re-declaration', 'desc' => 'Si rejete, le membre peut redeclarer avec une nouvelle preuve.'])

                    @include('admin.guide._info', ['text' => 'Quand toutes les contributions d\'un tour sont confirmees, le tour est automatiquement marque comme complete.'])
                    @include('admin.guide._warning', ['text' => 'Les penalites de retard sont calculees automatiquement selon les parametres de la tontine.'])

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Vue liste et vue matrice des contributions']) --}}
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 9 : Declarer un paiement --}}
            {{-- ============================================================ --}}
            <section id="declarer-paiement" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Declarer un paiement</h2>
                    </div>

                    @include('admin.guide._step', ['num' => 1, 'title' => 'Trouvez votre contribution', 'desc' => 'Depuis la page des contributions, trouvez votre ligne et cliquez "Declarer".'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Moyen de paiement', 'desc' => 'Choisissez : Orange Money, MTN Mobile Money, Wave, Virement bancaire ou Especes.'])
                    @include('admin.guide._step', ['num' => 3, 'title' => 'Details du paiement', 'desc' => 'Reference de transaction, numero de telephone de l\'envoyeur, date du paiement, montant.'])
                    @include('admin.guide._step', ['num' => 4, 'title' => 'Preuve de paiement', 'desc' => 'Joignez une capture d\'ecran de la transaction (optionnel mais recommande).'])
                    @include('admin.guide._step', ['num' => 5, 'title' => 'Validation', 'desc' => 'Ajoutez une note si necessaire et validez. Votre contribution passe au statut "DECLAREE".'])

                    @include('admin.guide._info', ['text' => 'Le systeme detecte automatiquement les paiements en double pour eviter les erreurs.'])
                    {{-- @include('admin.guide._screenshot', ['caption' =>'Formulaire de declaration de paiement avec upload de preuve']) --}}
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 10 : Chat --}}
            {{-- ============================================================ --}}
            <section id="chat" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Chat et messagerie</h2>
                    </div>

                    <p class="text-slate-600 dark:text-slate-400 mb-4">Chaque tontine dispose d'un chat integre pour permettre aux membres de communiquer.</p>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Fonctionnalites</h3>
                    <ul class="list-disc list-inside text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                        <li>Messages texte en temps reel (mise a jour automatique)</li>
                        <li>Envoi d'images</li>
                        <li>Indicateur de messages non lus (badge sur l'icone)</li>
                        <li>Accessible depuis la page de la tontine, onglet "Messages"</li>
                    </ul>

                    @include('admin.guide._info', ['text' => 'Seuls les membres actifs de la tontine peuvent participer au chat.'])
                    {{-- @include('admin.guide._screenshot', ['caption' =>'Interface de chat d\'une tontine']) --}}
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 11 : Echange de positions --}}
            {{-- ============================================================ --}}
            <section id="swap" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Echange de positions</h2>
                    </div>

                    <p class="text-slate-600 dark:text-slate-400 mb-4">Les membres peuvent demander a echanger leur position (et donc leur tour de benefice) avec un autre membre.</p>

                    @include('admin.guide._step', ['num' => 1, 'title' => 'Faire une demande', 'desc' => 'Depuis la page de la tontine, cliquez sur "Demander un echange".'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Selectionner le membre', 'desc' => 'Choisissez le membre avec qui vous souhaitez echanger.'])
                    @include('admin.guide._step', ['num' => 3, 'title' => 'Notification', 'desc' => 'Le membre cible recoit une notification et peut accepter ou refuser.'])
                    @include('admin.guide._step', ['num' => 4, 'title' => 'Echange effectue', 'desc' => 'Si accepte, les positions sont automatiquement echangees.'])

                    @include('admin.guide._info', ['text' => 'L\'admin peut voir toutes les demandes d\'echange en cours depuis l\'onglet "Echanges".'])
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 12 : Exports --}}
            {{-- ============================================================ --}}
            <section id="exports" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-lime-100 dark:bg-lime-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-lime-600 dark:text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Exports et rapports</h2>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 mb-6">
                        <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                            <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Exports CSV</h4>
                            <ul class="list-disc list-inside text-sm text-slate-600 dark:text-slate-400 space-y-1">
                                <li>Liste des contributions</li>
                                <li>Matrice des contributions</li>
                                <li>Liste des membres</li>
                                <li>Rapport complet</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                            <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Exports PDF</h4>
                            <ul class="list-disc list-inside text-sm text-slate-600 dark:text-slate-400 space-y-1">
                                <li>Rapport des contributions</li>
                                <li>Rapport financier</li>
                            </ul>
                        </div>
                    </div>

                    @include('admin.guide._step', ['num' => 1, 'title' => 'Naviguez', 'desc' => 'Depuis la page de la tontine, allez dans la section souhaitee (contributions, finances, membres).'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Exportez', 'desc' => 'Cliquez sur le bouton "Exporter CSV" ou "Exporter PDF".'])
                    @include('admin.guide._step', ['num' => 3, 'title' => 'Telechargement', 'desc' => 'Le fichier est telecharge automatiquement.'])
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 13 : Historique financier --}}
            {{-- ============================================================ --}}
            <section id="historique-financier" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Historique financier</h2>
                    </div>

                    <p class="text-slate-600 dark:text-slate-400 mb-4">Page personnelle accessible depuis le menu "Historique financier". Elle affiche :</p>
                    <ul class="list-disc list-inside text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                        <li>Toutes vos contributions confirmees, toutes tontines confondues</li>
                        <li>Les montants recus en tant que beneficiaire</li>
                        <li>Filtres par periode et par tontine</li>
                    </ul>

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Historique financier personnel']) --}}
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTION 14 : Parametres utilisateur --}}
            {{-- ============================================================ --}}
            <section id="parametres-utilisateur" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Parametres utilisateur</h2>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-4 mb-4">
                        <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                            <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Profil</h4>
                            <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
                                <li>Modifier nom, email, telephone</li>
                                <li>Changer la photo de profil</li>
                                <li>Supprimer l'avatar</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                            <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Securite</h4>
                            <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
                                <li>Changer le mot de passe</li>
                                <li>Sessions actives (appareils)</li>
                                <li>Revoquer une session</li>
                            </ul>
                        </div>
                        <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                            <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Notifications</h4>
                            <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
                                <li>Activer/desactiver emails</li>
                                <li>Activer/desactiver push</li>
                                <li>Mode digest (resume quotidien)</li>
                            </ul>
                        </div>
                    </div>

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Page des parametres utilisateur']) --}}
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- SECTIONS ADMIN (15-20) --}}
            {{-- ============================================================ --}}
            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t-2 border-[#3C50E0]/20"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="px-6 py-2 bg-slate-100 dark:bg-slate-900 text-[#3C50E0] font-bold text-sm uppercase tracking-wider rounded-full border-2 border-[#3C50E0]/20">Espace Administration</span>
                </div>
            </div>

            {{-- SECTION 15 : Admin Dashboard --}}
            <section id="admin-dashboard" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-[#3C50E0]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#3C50E0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Administration - Tableau de bord</h2>
                    </div>

                    <p class="text-slate-600 dark:text-slate-400 mb-4">Vue d'ensemble de toute la plateforme avec indicateurs cles.</p>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">KPIs (6 cartes)</h3>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
                        @foreach(['Utilisateurs (nouveaux ce mois)', 'Tontines (actives)', 'Contributions confirmees (FCFA)', 'Membres total', 'Requetes en attente', 'Tours en cours'] as $kpi)
                        <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-700/30 text-center">
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $kpi }}</p>
                        </div>
                        @endforeach
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Graphiques et tableaux</h3>
                    <ul class="list-disc list-inside text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                        <li>Evolution des inscriptions sur 6 mois</li>
                        <li>Contributions confirmees vs declarees sur 6 mois</li>
                        <li>Distribution des tontines par statut</li>
                        <li>Top 5 des tontines par montant collecte</li>
                        <li>Activite recente (10 dernieres actions)</li>
                        <li>Derniers utilisateurs et tontines</li>
                    </ul>

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Tableau de bord administrateur avec KPIs et graphiques']) --}}
                </div>
            </section>

            {{-- SECTION 16 : Admin Users --}}
            <section id="admin-users" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-[#3C50E0]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#3C50E0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Administration - Gestion des utilisateurs</h2>
                    </div>

                    @include('admin.guide._step', ['num' => 1, 'title' => 'Acceder a la liste', 'desc' => 'Menu Administration puis "Utilisateurs". Liste paginee avec recherche par nom, telephone, email.'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Filtrer', 'desc' => 'Filtrez par statut : actif, suspendu, verrouille.'])

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3 mt-4">Actions sur un utilisateur</h3>
                    <ul class="list-disc list-inside text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                        <li>Voir le profil detaille (tontines, contributions)</li>
                        <li>Suspendre le compte (empeche la connexion)</li>
                        <li>Reactiver un compte suspendu</li>
                        <li>Deverrouiller un compte verrouille</li>
                        <li>Impersonner (se connecter en tant que cet utilisateur)</li>
                    </ul>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Creer un utilisateur</h3>
                    @include('admin.guide._step', ['num' => 1, 'title' => 'Nouvel utilisateur', 'desc' => 'Cliquez "Nouvel utilisateur" et remplissez nom, telephone, email.'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Mot de passe temporaire', 'desc' => 'Un mot de passe est genere et envoye par SMS. L\'utilisateur devra le changer a sa premiere connexion.'])

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3 mt-4">Actions en masse</h3>
                    <ul class="list-disc list-inside text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                        <li>Activer plusieurs comptes</li>
                        <li>Suspendre plusieurs comptes</li>
                        <li>Exporter la liste en CSV</li>
                    </ul>

                    @include('admin.guide._warning', ['text' => 'Il est impossible de suspendre un compte administrateur.'])
                    {{-- @include('admin.guide._screenshot', ['caption' =>'Page de gestion des utilisateurs avec filtres et actions']) --}}
                </div>
            </section>

            {{-- SECTION 17 : Admin Requetes --}}
            <section id="admin-requetes" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-[#3C50E0]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#3C50E0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Administration - Requetes</h2>
                    </div>

                    <p class="text-slate-600 dark:text-slate-400 mb-4">Les utilisateurs peuvent soumettre des requetes de support. Types : echange de tour, retrait, exclusion d'un membre, autre demande.</p>

                    @include('admin.guide._step', ['num' => 1, 'title' => 'Consulter', 'desc' => 'Menu Administration puis "Requetes". Filtrez par statut (en attente, en cours, resolue, rejetee) et par type.'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Traiter', 'desc' => 'Cliquez sur une requete pour voir les details.'])
                    @include('admin.guide._step', ['num' => 3, 'title' => 'Repondre', 'desc' => 'Redigez une reponse et changez le statut. L\'utilisateur est notifie automatiquement.'])

                    {{-- @include('admin.guide._screenshot', ['caption' =>'Liste et detail des requetes administrateur']) --}}
                </div>
            </section>

            {{-- SECTION 18 : Admin Activite --}}
            <section id="admin-activite" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-[#3C50E0]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#3C50E0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Administration - Journal d'activite</h2>
                    </div>

                    <p class="text-slate-600 dark:text-slate-400 mb-4">Historique complet de toutes les actions effectuees sur la plateforme.</p>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Filtres disponibles</h3>
                    <ul class="list-disc list-inside text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                        <li>Par tontine</li>
                        <li>Par utilisateur</li>
                        <li>Par type d'action (creation, modification, suppression, etc.)</li>
                    </ul>

                    <p class="text-slate-600 dark:text-slate-400 mb-4">Chaque entree affiche : date/heure, utilisateur, action, objet concerne, tontine associee.</p>

                    @include('admin.guide._info', ['text' => 'Le journal d\'activite est un outil precieux pour l\'audit et la resolution de litiges.'])
                    {{-- @include('admin.guide._screenshot', ['caption' =>'Journal d\'activite avec filtres']) --}}
                </div>
            </section>

            {{-- SECTION 19 : Admin Parametres --}}
            <section id="admin-parametres" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-[#3C50E0]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#3C50E0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Administration - Parametres plateforme</h2>
                    </div>

                    <p class="text-slate-600 dark:text-slate-400 mb-4">Configuration globale de la plateforme, organisee en onglets :</p>

                    <div class="space-y-4 mb-6">
                        <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                            <h4 class="font-semibold text-slate-800 dark:text-white mb-2">General</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Nom de la plateforme, contact support, mode maintenance, autoriser la creation de tontines, limites (nombre max de tontines par utilisateur, contributions min/max en FCFA, nombre max de membres), delai d'archivage.</p>
                        </div>
                        <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                            <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Notifications</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Activer les emails, activer les notifications push, jours de rappel avant echeance.</p>
                        </div>
                        <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                            <h4 class="font-semibold text-slate-800 dark:text-white mb-2">SMS et WhatsApp</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Fournisseurs SMS (Twilio, Infobip, Orange, LeTexto) et WhatsApp (Twilio, Meta) avec configuration des cles API.</p>
                        </div>
                        <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700">
                            <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Paiements</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Passerelles de paiement (Orange Money, MTN MoMo, Moov, Wave) avec cles API pour chaque fournisseur.</p>
                        </div>
                    </div>

                    @include('admin.guide._warning', ['text' => 'Le mode maintenance bloque l\'acces a tous les utilisateurs sauf les administrateurs.'])
                    {{-- @include('admin.guide._screenshot', ['caption' =>'Parametres plateforme - onglet General et configuration SMS']) --}}
                </div>
            </section>

            {{-- SECTION 20 : Impersonation --}}
            <section id="admin-impersonation" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-[#3C50E0]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#3C50E0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Administration - Impersonation</h2>
                    </div>

                    <p class="text-slate-600 dark:text-slate-400 mb-4">Permet a un admin de se connecter temporairement en tant qu'un autre utilisateur pour diagnostiquer un probleme.</p>

                    @include('admin.guide._step', ['num' => 1, 'title' => 'Selectionner l\'utilisateur', 'desc' => 'Menu Administration, Utilisateurs, puis selectionnez un utilisateur.'])
                    @include('admin.guide._step', ['num' => 2, 'title' => 'Se connecter en tant que', 'desc' => 'Cliquez "Se connecter en tant que". Une banniere orange apparait en haut.'])
                    @include('admin.guide._step', ['num' => 3, 'title' => 'Naviguer', 'desc' => 'Naviguez comme si vous etiez cet utilisateur pour diagnostiquer le probleme.'])
                    @include('admin.guide._step', ['num' => 4, 'title' => 'Revenir', 'desc' => 'Cliquez "Revenir a mon compte" dans la banniere pour arreter l\'impersonation.'])

                    @include('admin.guide._warning', ['text' => 'L\'impersonation est enregistree dans le journal d\'activite. Il est impossible d\'impersonner un autre administrateur.'])
                    {{-- @include('admin.guide._screenshot', ['caption' =>'Banniere d\'impersonation active en haut de page']) --}}
                </div>
            </section>

            {{-- SECTION 21 : Notifications --}}
            <section id="notifications" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Notifications</h2>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Canaux de notification</h3>
                    <div class="grid sm:grid-cols-3 gap-3 mb-6">
                        <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/30 text-center">
                            <p class="font-semibold text-slate-800 dark:text-white mb-1">In-app</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Cloche dans l'en-tete avec badge compteur</p>
                        </div>
                        <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/30 text-center">
                            <p class="font-semibold text-slate-800 dark:text-white mb-1">Email</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Notifications et digest quotidien</p>
                        </div>
                        <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/30 text-center">
                            <p class="font-semibold text-slate-800 dark:text-white mb-1">Push</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Notifications navigateur</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3">Evenements notifies</h3>
                    <ul class="list-disc list-inside text-slate-600 dark:text-slate-400 space-y-1 mb-4">
                        <li>Nouvelle contribution declaree (admin/tresorier)</li>
                        <li>Contribution confirmee/rejetee (membre)</li>
                        <li>Tour demarre / complete / fonds a decaisser</li>
                        <li>Nouveau membre demandant a rejoindre</li>
                        <li>Requete traitee</li>
                        <li>Rappel avant echeance</li>
                        <li>Retard de paiement</li>
                    </ul>

                    @include('admin.guide._info', ['text' => 'Configurez vos preferences de notification dans Parametres puis Notifications.'])
                </div>
            </section>

            {{-- SECTION 22 : Glossaire --}}
            <section id="glossaire" class="scroll-mt-24">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Glossaire</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 font-semibold text-slate-800 dark:text-white">Terme</th>
                                    <th class="text-left py-3 px-4 font-semibold text-slate-800 dark:text-white">Definition</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @php
                                $glossary = [
                                    ['Tontine', 'Groupe d\'epargne rotative ou chaque membre contribue et recoit a tour de role.'],
                                    ['Tour', 'Cycle de collecte au sein d\'une tontine, avec un beneficiaire designe.'],
                                    ['Contribution', 'Paiement d\'un membre pour un tour donne.'],
                                    ['Beneficiaire', 'Membre qui recoit la cagnotte lors de son tour.'],
                                    ['Cagnotte', 'Somme totale collectee lors d\'un tour.'],
                                    ['Parts', 'Multiplicateur de contribution (1 part = montant de base, 2 parts = double).'],
                                    ['FCFA', 'Franc CFA, monnaie utilisee en Cote d\'Ivoire.'],
                                    ['Admin', 'Createur et gestionnaire d\'une tontine.'],
                                    ['Tresorier', 'Role avec droits de validation des paiements.'],
                                    ['Declaration', 'Acte du membre annoncant qu\'il a effectue son paiement.'],
                                    ['Confirmation', 'Validation par l\'admin/tresorier de la declaration de paiement.'],
                                    ['Impersonation', 'Fonctionnalite admin permettant de naviguer en tant qu\'un autre utilisateur.'],
                                    ['OTP', 'One-Time Password, code envoye par SMS pour verification.'],
                                    ['VAPID', 'Cles pour les notifications push navigateur.'],
                                ];
                                @endphp
                                @foreach($glossary as $g)
                                <tr>
                                    <td class="py-3 px-4 font-medium text-slate-800 dark:text-white whitespace-nowrap">{{ $g[0] }}</td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400">{{ $g[1] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </div>
    </div>

    {{-- Bouton retour en haut --}}
    <div x-data="{ show: false }" @scroll.window="show = window.scrollY > 500" class="fixed bottom-24 lg:bottom-8 right-6 z-40">
        <button x-show="show" x-transition @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                class="w-12 h-12 rounded-full bg-[#3C50E0] text-white shadow-lg hover:bg-[#1C3FB7] transition flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
        </button>
    </div>

    @push('scripts')
    <script>
    function guideNav() {
        const sectionIds = [
            'introduction','concepts','auth','dashboard','creer-tontine','membres',
            'tours','contributions','declarer-paiement','chat','swap','exports',
            'historique-financier','parametres-utilisateur',
            'admin-dashboard','admin-users','admin-requetes','admin-activite','admin-parametres','admin-impersonation',
            'notifications','glossaire'
        ];
        const labels = [
            'Introduction','Concepts de base','Inscription et Connexion','Tableau de bord',
            'Creer une tontine','Gerer les membres','Les tours','Les contributions',
            'Declarer un paiement','Chat','Echange de positions','Exports et rapports',
            'Historique financier','Parametres utilisateur',
            'Admin - Dashboard','Admin - Utilisateurs','Admin - Requetes','Admin - Activite','Admin - Parametres','Admin - Impersonation',
            'Notifications','Glossaire'
        ];
        const shorts = [
            'Introduction','Concepts','Connexion','Dashboard',
            'Creer tontine','Membres','Tours','Contributions',
            'Paiement','Chat','Echange','Exports',
            'Historique','Parametres',
            'Admin Dashboard','Admin Users','Admin Requetes','Admin Activite','Admin Parametres','Impersonation',
            'Notifications','Glossaire'
        ];

        return {
            active: 'introduction',
            sections: sectionIds.map((id, i) => ({ id, label: labels[i], short: shorts[i], num: i + 1 })),
            scrollTo(id) {
                const el = document.getElementById(id);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth' });
                    this.active = id;
                }
            },
            init() {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.active = entry.target.id;
                        }
                    });
                }, { rootMargin: '-20% 0px -60% 0px' });

                sectionIds.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) observer.observe(el);
                });
            }
        };
    }
    </script>
    @endpush
</x-layouts.app>
