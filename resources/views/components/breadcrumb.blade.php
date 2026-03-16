@props(['items' => []])

<nav aria-label="Fil d'Ariane" class="mb-4">
    <ol class="flex flex-wrap items-center gap-1 text-sm" itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach($items as $index => $item)
            <li class="flex items-center gap-1" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}" class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors" itemprop="item">
                        <span itemprop="name">{{ $item['label'] }}</span>
                    </a>
                @else
                    <span class="text-slate-800 dark:text-white font-medium" itemprop="name">{{ $item['label'] }}</span>
                @endif
                <meta itemprop="position" content="{{ $index + 1 }}" />
            </li>
            @if(!$loop->last)
                <li class="text-slate-400 dark:text-slate-500" aria-hidden="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
