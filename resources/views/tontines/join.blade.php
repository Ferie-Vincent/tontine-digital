<x-layouts.app title="Rejoindre une tontine">
    <x-slot:header>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Rejoindre une tontine</h2>
    </x-slot:header>

    <div class="min-h-[60vh] flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <!-- Card Container -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-xl overflow-hidden">

                <!-- Gradient Header Section -->
                <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 px-8 py-10 text-center">
                    <!-- Large Icon -->
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Rejoindre une tontine</h3>
                    <p class="text-white/80 text-sm">Entrez le code d'invitation fourni par l'administrateur</p>
                </div>

                <!-- Form Section -->
                <div class="px-8 py-8">
                    <form method="POST" action="{{ route('tontines.join.submit') }}" class="space-y-6">
                        @csrf

                        <!-- Code Input Field -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Code d'invitation
                            </label>
                            <input
                                type="text"
                                name="code"
                                id="code"
                                value="{{ request('code', old('code')) }}"
                                placeholder="XXXXXXXX"
                                maxlength="8"
                                required
                                class="w-full px-4 py-4 text-center text-2xl font-mono tracking-[0.3em] uppercase bg-gray-50 dark:bg-slate-700 border-2 border-gray-200 dark:border-slate-600 rounded-xl text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition-all duration-200"
                            >
                            @error('code')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button with Gradient -->
                        <button
                            type="submit"
                            class="w-full py-4 px-6 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 hover:from-indigo-600 hover:via-purple-600 hover:to-pink-600 text-white font-semibold text-lg rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-purple-500/30"
                        >
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Rejoindre la tontine
                            </span>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200 dark:border-slate-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-white dark:bg-slate-800 text-gray-500 dark:text-gray-400">ou</span>
                        </div>
                    </div>

                    <!-- Back Link -->
                    <a
                        href="{{ route('tontines.index') }}"
                        class="flex items-center justify-center gap-2 w-full py-3 px-4 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-colors duration-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à mes tontines
                    </a>
                </div>
            </div>

            <!-- Help Text -->
            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                Vous n'avez pas de code ? Demandez-le à l'administrateur de la tontine que vous souhaitez rejoindre.
            </p>
        </div>
    </div>
</x-layouts.app>
