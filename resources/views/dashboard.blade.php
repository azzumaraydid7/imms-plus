<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard &middot; {{ config('app.name', 'IMMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-500 text-sm font-semibold text-white">IM</div>
                <span class="font-semibold tracking-tight">IMMS</span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                    Log out
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-10">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                Welcome back, {{ auth()->user()->name }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">{{ auth()->user()->email }}</p>

            @php($user = auth()->user())

            <div class="mt-8">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Roles</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse ($user->roleSlugs() as $role)
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ $role }}</span>
                    @empty
                        <span class="text-sm text-slate-400">No roles assigned.</span>
                    @endforelse
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Permissions ({{ count($user->permissionSlugs()) }})</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse ($user->permissionSlugs() as $permission)
                        <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $permission }}</span>
                    @empty
                        <span class="text-sm text-slate-400">No permissions granted.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</body>
</html>
