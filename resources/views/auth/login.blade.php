<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in &middot; {{ config('app.name', 'IMMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-12">
        <div class="pointer-events-none absolute -top-32 -right-24 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-sky-500/20 blur-3xl"></div>

        <div class="relative w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-500 text-lg font-semibold text-white shadow-lg shadow-indigo-500/30">
                    IM
                </div>
                <h1 class="text-2xl font-semibold tracking-tight text-white">Welcome back</h1>
                <p class="mt-1 text-sm text-slate-400">Sign in to your IMMS account to continue.</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur">
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-300">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@imjpj.my"
                            class="block w-full rounded-xl border border-white/10 bg-slate-900/60 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/40"
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-300">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="block w-full rounded-xl border border-white/10 bg-slate-900/60 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/40"
                        >
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-400">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            class="h-4 w-4 rounded border-white/20 bg-slate-900 text-indigo-500 focus:ring-indigo-500/40"
                        >
                        Remember me
                    </label>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-950"
                    >
                        Sign in
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-slate-500">
                Protected system for Institut Memandu JPJ (IMJPJ).
            </p>
        </div>
    </div>
</body>
</html>
