@extends('layouts.desktop')

@section('title', 'Overview')

@section('content')
    <div class="space-y-6">

        {{-- ── Hero ────────────────────────────────────────────────────────── --}}
        <section class="animate-rise" style="animation-delay: 40ms">
            <div class="relative overflow-hidden rounded-2xl border border-white/[0.07] bg-gradient-to-br from-ink-800/80 via-ink-850/60 to-ink-900/40 p-7">
                <div class="pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full bg-signal-500/20 blur-3xl"></div>
                <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-signal-400/50 to-transparent"></div>

                <div class="relative flex flex-wrap items-start justify-between gap-6">
                    <div class="min-w-0">
                        <p class="font-mono text-[11px] font-medium uppercase tracking-[0.2em] text-signal-300/80">
                            {{ $greeting }}
                        </p>
                        <h1 class="mt-2 truncate text-[28px] font-semibold leading-tight tracking-tight text-white">
                            {{ $user->name }}
                        </h1>
                        <p class="mt-1.5 font-mono text-[12.5px] text-slate-500">{{ $user->email }}</p>

                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            @forelse ($roles as $role)
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-signal-400/25 bg-signal-500/[0.1] px-3 py-1 text-[12px] font-medium text-signal-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-signal-400"></span>
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="rounded-full border border-amber-400/25 bg-amber-400/[0.08] px-3 py-1 text-[12px] font-medium text-amber-300">
                                    No role assigned
                                </span>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-2 text-right">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/[0.08] bg-white/[0.03] px-3 py-1.5">
                            <span class="h-1.5 w-1.5 rounded-full {{ $isNative ? 'bg-emerald-400 animate-beacon' : 'bg-amber-400' }}"></span>
                            <span class="font-mono text-[10.5px] uppercase tracking-[0.16em] text-slate-300">
                                {{ $isNative ? 'Native shell' : 'Browser session' }}
                            </span>
                        </span>
                        <span class="font-mono text-[10.5px] uppercase tracking-[0.16em] text-slate-600">
                            {{ app()->environment() }} · {{ now()->format('d M Y') }}
                        </span>
                        <span class="font-mono text-[10.5px] text-slate-600">
                            last sign-in {{ optional($user->last_login_at)->diffForHumans() ?? 'just now' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── Stat tiles ──────────────────────────────────────────────────── --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $index => $stat)
                <div
                    class="animate-rise group rounded-xl border border-white/[0.07] bg-ink-850/50 p-4 transition-colors duration-200 hover:border-white/[0.13] hover:bg-ink-800/60"
                    style="animation-delay: {{ 100 + ($index * 55) }}ms"
                >
                    <p class="font-mono text-[10px] font-medium uppercase tracking-[0.18em] text-slate-500">
                        {{ $stat['label'] }}
                    </p>
                    <p class="mt-2.5 font-mono text-[26px] font-medium leading-none tabular-nums text-slate-100">
                        {{ $stat['value'] }}
                    </p>
                    <p class="mt-2 truncate text-[11.5px] text-slate-500" title="{{ $stat['caption'] }}">
                        {{ $stat['caption'] }}
                    </p>
                </div>
            @endforeach
        </section>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- ── Roles ───────────────────────────────────────────────────── --}}
            <section class="animate-rise xl:col-span-2" style="animation-delay: 320ms">
                <div class="flex h-full flex-col rounded-2xl border border-white/[0.07] bg-ink-850/40">
                    <header class="flex items-center justify-between border-b border-white/[0.06] px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-[13px] font-semibold tracking-tight text-slate-200">Roles</h2>
                            <span class="rounded-md bg-white/[0.05] px-1.5 py-0.5 font-mono text-[10px] text-slate-400">
                                {{ $roles->count() }}
                            </span>
                        </div>
                        <span class="font-mono text-[10px] uppercase tracking-[0.16em] text-slate-600">RBAC</span>
                    </header>

                    <div class="divide-y divide-white/[0.05]">
                        @forelse ($roles as $role)
                            <article class="group flex items-start gap-4 px-5 py-4 transition-colors duration-150 hover:bg-white/[0.02]">
                                <span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-signal-400/20 bg-signal-500/[0.08] text-signal-300">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-[17px] w-[17px]">
                                        <path d="M12 3l7 3v5.5c0 4.3-3 7.6-7 9-4-1.4-7-4.7-7-9V6z"/><path d="M9.5 12l1.8 1.8L15 10"/>
                                    </svg>
                                </span>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-[13.5px] font-medium text-slate-100">{{ $role->name }}</h3>
                                        <span class="rounded border border-white/[0.08] px-1.5 py-0.5 font-mono text-[10px] text-slate-400">
                                            {{ $role->slug }}
                                        </span>
                                        @if ($role->is_system)
                                            <span class="rounded-full border border-amber-400/25 bg-amber-400/[0.08] px-2 py-0.5 font-mono text-[9.5px] uppercase tracking-[0.12em] text-amber-300/90">
                                                system
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-[12px] leading-relaxed text-slate-500">
                                        {{ $role->description ?: 'No description recorded for this role.' }}
                                    </p>
                                </div>

                                <div class="shrink-0 text-right">
                                    <p class="font-mono text-[15px] tabular-nums text-slate-200">{{ $role->permissions->count() }}</p>
                                    <p class="font-mono text-[9.5px] uppercase tracking-[0.14em] text-slate-600">perms</p>
                                </div>
                            </article>
                        @empty
                            <div class="px-5 py-10 text-center">
                                <p class="text-[13px] text-slate-500">This account has no roles yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- ── Module readiness ────────────────────────────────────────── --}}
            <section class="animate-rise" style="animation-delay: 380ms">
                <div class="flex h-full flex-col rounded-2xl border border-white/[0.07] bg-ink-850/40">
                    <header class="flex items-center justify-between border-b border-white/[0.06] px-5 py-3.5">
                        <h2 class="text-[13px] font-semibold tracking-tight text-slate-200">Module readiness</h2>
                        <span class="font-mono text-[10px] uppercase tracking-[0.16em] text-slate-600">app/Modules</span>
                    </header>

                    <ul class="grid grid-cols-2 gap-px bg-white/[0.05] p-px">
                        @foreach ($modules as $module)
                            @php($isLive = $module['status'] === 'live')
                            <li class="flex items-center gap-2 bg-ink-850/80 px-3 py-2.5">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $isLive ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
                                <span class="truncate text-[12px] {{ $isLive ? 'font-medium text-slate-200' : 'text-slate-500' }}">
                                    {{ $module['label'] }}
                                </span>
                                @if ($isLive)
                                    <span class="ml-auto font-mono text-[9px] uppercase tracking-[0.12em] text-emerald-400/80">live</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        </div>

        {{-- ── Permissions ─────────────────────────────────────────────────── --}}
        <section class="animate-rise" style="animation-delay: 440ms">
            <div class="rounded-2xl border border-white/[0.07] bg-ink-850/40">
                <header class="flex items-center justify-between border-b border-white/[0.06] px-5 py-3.5">
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-[13px] font-semibold tracking-tight text-slate-200">Effective permissions</h2>
                        <span class="rounded-md bg-white/[0.05] px-1.5 py-0.5 font-mono text-[10px] text-slate-400">
                            {{ $permissions->count() }}
                        </span>
                    </div>
                    <span class="font-mono text-[10px] uppercase tracking-[0.16em] text-slate-600">
                        {{ $permissionGroupCount }} {{ Str::plural('group', $permissionGroupCount) }}
                    </span>
                </header>

                @forelse ($permissionGroups as $group => $groupPermissions)
                    <div class="border-b border-white/[0.05] px-5 py-4 last:border-b-0">
                        <div class="mb-2.5 flex items-center gap-2">
                            <span class="font-mono text-[10px] font-medium uppercase tracking-[0.16em] text-signal-300/80">
                                {{ $group }}
                            </span>
                            <span class="h-px flex-1 bg-white/[0.05]"></span>
                            <span class="font-mono text-[10px] text-slate-600">{{ $groupPermissions->count() }}</span>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($groupPermissions as $permission)
                                <span
                                    class="group inline-flex items-center gap-1.5 rounded-md border border-white/[0.07] bg-ink-800/60 px-2.5 py-1 transition-colors duration-150 hover:border-signal-400/30 hover:bg-signal-500/[0.08]"
                                    title="{{ $permission->description ?: $permission->name }}"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 text-slate-600 transition-colors group-hover:text-signal-300">
                                        <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>
                                    </svg>
                                    <span class="font-mono text-[11px] text-slate-400 transition-colors group-hover:text-slate-200">
                                        {{ $permission->slug }}
                                    </span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center">
                        <p class="text-[13px] text-slate-500">No permissions are granted through this account's roles.</p>
                        <p class="mt-1 font-mono text-[11px] text-slate-600">Assign a role to inherit its permission set.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
