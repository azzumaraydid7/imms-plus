@php
    $user = auth()->user();

    // `nativephp-internal.running` is set by NativePHP when the app is served by
    // the Electron shell. In the browser we drop the OS chrome affordances.
    $isNative = (bool) config('nativephp-internal.running');
    $isMac = PHP_OS_FAMILY === 'Darwin';

    $showTrafficLightGap = $isNative && $isMac;
    $showWindowControls = $isNative && ! $isMac;

    // Anything without a route renders as a disabled "soon" row.
    $overview = config('navigation.overview');
    $sections = config('navigation.sections', []);

    $overviewActive = request()->routeIs($overview['route']);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &middot; {{ config('app.name', 'IMMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="deck-grain deck-grid h-screen overflow-hidden bg-ink-950 font-sans text-slate-300 antialiased">
    <div class="relative z-10 flex h-screen flex-col">

        {{-- ── Title bar / drag handle ─────────────────────────────────────── --}}
        <header class="drag-region relative z-30 flex h-12 shrink-0 items-center gap-3 border-b border-white/[0.06] bg-ink-900/70 backdrop-blur-xl {{ $showTrafficLightGap ? 'pl-[92px]' : 'pl-4' }} pr-2">
            <a href="{{ route('dashboard') }}" class="no-drag flex items-center gap-2.5 rounded-lg px-1.5 py-1 transition-colors hover:bg-white/[0.04]">
                <span class="grid h-6 w-6 place-items-center rounded-md bg-gradient-to-br from-signal-400 to-indigo-600 text-[10px] font-bold tracking-tight text-white shadow-[0_0_18px_-4px] shadow-signal-500/70">IM</span>
                <span class="text-[13px] font-semibold tracking-tight text-slate-200">IMMS</span>
            </a>

            <span class="select-none text-slate-700">/</span>
            <span class="text-[13px] font-medium text-slate-400">@yield('title', 'Dashboard')</span>

            <div class="no-drag ml-auto flex items-center gap-2">
                <span class="hidden rounded-full border border-white/[0.07] bg-white/[0.03] px-2.5 py-1 font-mono text-[10px] font-medium uppercase tracking-[0.14em] text-slate-400 sm:inline-block">
                    {{ app()->environment() }}
                </span>

                <span class="hidden items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/[0.07] px-2.5 py-1 md:inline-flex">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-beacon"></span>
                    <span class="font-mono text-[10px] uppercase tracking-[0.14em] text-emerald-300/90">Live</span>
                </span>

                <span data-clock class="px-1 font-mono text-[11px] tabular-nums text-slate-500">--:--:--</span>

                @if ($showWindowControls)
                    <div class="ml-1 flex items-center gap-1 border-l border-white/[0.06] pl-2">
                        @foreach ([
                            ['action' => 'minimize', 'label' => 'Minimise', 'route' => 'window.minimize', 'icon' => '<path d="M5 12h14"/>', 'hover' => 'hover:bg-white/[0.06] hover:text-slate-100'],
                            ['action' => 'maximize', 'label' => 'Maximise', 'route' => 'window.maximize', 'icon' => '<rect x="5" y="5" width="14" height="14" rx="2"/>', 'hover' => 'hover:bg-white/[0.06] hover:text-slate-100'],
                            ['action' => 'close', 'label' => 'Close', 'route' => 'window.close', 'icon' => '<path d="M6 6l12 12M18 6 6 18"/>', 'hover' => 'hover:bg-rose-500/90 hover:text-white'],
                        ] as $control)
                            <div class="group relative">
                                <button
                                    type="button"
                                    data-window-action="{{ $control['action'] }}"
                                    data-endpoint="{{ route($control['route']) }}"
                                    aria-label="{{ $control['label'] }}"
                                    class="grid h-7 w-7 place-items-center rounded-lg text-slate-400 transition-colors duration-150 {{ $control['hover'] }}"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="h-[13px] w-[13px]">{!! $control['icon'] !!}</svg>
                                </button>
                                <span class="pointer-events-none absolute right-0 top-full z-50 mt-2 whitespace-nowrap rounded-md border border-white/10 bg-ink-800 px-2 py-1 text-[11px] font-medium text-slate-200 opacity-0 shadow-xl shadow-black/60 transition-opacity duration-150 group-hover:opacity-100">
                                    {{ $control['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </header>

        <div class="relative flex min-h-0 flex-1">

            {{-- ── Module rail ─────────────────────────────────────────────── --}}
            <nav class="deck-scroll flex w-[248px] shrink-0 flex-col overflow-y-auto border-r border-white/[0.06] bg-ink-900/50 px-3 py-4">
                <span class="mb-3 block px-2 font-mono text-[10px] font-medium uppercase tracking-[0.18em] text-slate-600">
                    Institut Memandu JPJ
                </span>

                <a
                    href="{{ route($overview['route']) }}"
                    class="group relative flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[13px] font-medium transition-colors duration-150 {{ $overviewActive ? 'bg-signal-500/[0.12] text-signal-200' : 'text-slate-400 hover:bg-white/[0.04] hover:text-slate-200' }}"
                >
                    @if ($overviewActive)
                        <span class="absolute -left-3 top-1/2 h-5 w-[2px] -translate-y-1/2 rounded-r-full bg-signal-400 shadow-[0_0_12px_1px] shadow-signal-500/60"></span>
                    @endif
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-[17px] w-[17px] shrink-0">
                        {!! $overview['icon'] !!}
                    </svg>
                    {{ $overview['label'] }}
                </a>

                @foreach ($sections as $section)
                    <span class="mb-1.5 mt-5 block px-2 font-mono text-[10px] font-medium uppercase tracking-[0.18em] text-slate-600">
                        {{ $section['label'] }}
                    </span>

                    @foreach ($section['items'] as $item)
                        @php($isLive = $item['route'] !== null && Route::has($item['route']))

                        @if ($isLive)
                            <a
                                href="{{ route($item['route']) }}"
                                class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[13px] font-medium text-slate-400 transition-colors duration-150 hover:bg-white/[0.04] hover:text-slate-200"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-[17px] w-[17px] shrink-0">{!! $item['icon'] !!}</svg>
                                {{ $item['label'] }}
                            </a>
                        @else
                            <div class="group flex cursor-not-allowed items-center gap-2.5 rounded-lg px-2.5 py-2 text-[13px] font-medium text-slate-500 transition-colors duration-150 hover:bg-white/[0.02]">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-[17px] w-[17px] shrink-0 text-slate-600">{!! $item['icon'] !!}</svg>
                                {{ $item['label'] }}
                                <span class="ml-auto rounded border border-white/[0.07] px-1.5 py-0.5 font-mono text-[9px] uppercase tracking-[0.12em] text-slate-600 transition-colors duration-150 group-hover:border-signal-500/30 group-hover:text-signal-300/80">
                                    Soon
                                </span>
                            </div>
                        @endif
                    @endforeach
                @endforeach

                {{-- Session footer --}}
                <div class="mt-auto pt-6">
                    <div class="h-px bg-white/[0.06]"></div>
                    <div class="mt-3 flex items-center gap-2.5 px-2 py-1">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-white/10 bg-ink-800 text-[11px] font-semibold text-slate-300">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[12.5px] font-medium text-slate-200">{{ $user->name }}</p>
                            <p class="truncate font-mono text-[10px] uppercase tracking-[0.12em] text-slate-600">
                                {{ $user->roleSlugs() ? implode(' · ', $user->roleSlugs()) : 'no role' }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                aria-label="Sign out"
                                class="group relative grid h-7 w-7 place-items-center rounded-lg text-slate-500 transition-colors duration-150 hover:bg-rose-500/10 hover:text-rose-300"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-[15px] w-[15px]">
                                    <path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="M10 17l-5-5 5-5"/><path d="M5 12h10"/>
                                </svg>
                                <span class="pointer-events-none absolute bottom-full right-0 z-50 mb-2 whitespace-nowrap rounded-md border border-white/10 bg-ink-800 px-2 py-1 text-[11px] font-medium text-slate-200 opacity-0 shadow-xl shadow-black/60 transition-opacity duration-150 group-hover:opacity-100">
                                    Sign out
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            {{-- ── Content ─────────────────────────────────────────────────── --}}
            <main class="min-w-0 flex-1 overflow-y-auto">
                <div class="mx-auto max-w-[1240px] px-7 py-7">
                    @yield('content')
                </div>
            </main>
        </div>

        {{-- ── Status bar ──────────────────────────────────────────────────── --}}
        <footer class="relative z-30 flex h-7 shrink-0 items-center gap-3 border-t border-white/[0.06] bg-ink-900/70 px-4 font-mono text-[10px] uppercase tracking-[0.12em] text-slate-600 backdrop-blur-xl">
            <span class="flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full {{ $isNative ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                {{ $isNative ? 'Native shell' : 'Browser session' }}
            </span>
            <span class="text-slate-800">/</span>
            <span class="truncate normal-case tracking-normal text-slate-500">{{ $user->email }}</span>

            <span class="ml-auto hidden items-center gap-3 lg:flex">
                <span>Laravel {{ app()->version() }}</span>
                <span class="text-slate-800">/</span>
                <span>PHP {{ PHP_VERSION }}</span>
                <span class="text-slate-800">/</span>
                <span>v{{ config('nativephp.version', '1.0.0') }}</span>
            </span>
        </footer>
    </div>
</body>
</html>
