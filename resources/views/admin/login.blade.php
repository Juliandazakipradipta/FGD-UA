<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - ULUL ALBAB & CAI 47</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4FBF7] text-slate-800 antialiased min-h-screen flex items-center justify-center px-5 font-sans">
    
    <!-- Background Ambient Glow -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-5xl h-96 pointer-events-none overflow-hidden z-0">
        <div class="absolute top-10 left-1/3 w-80 h-80 bg-emerald-300/20 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-sm relative z-10">
        <div class="text-center mb-6">
            <!-- Side-by-side Dual Logos -->
            <div class="flex items-center justify-center gap-3 bg-white/90 p-2.5 px-4 rounded-2xl border border-emerald-100/90 shadow-xs inline-flex mb-3">
                <img src="{{ asset('images/logo-ulul-albab.png') }}" alt="MT Ulul Albab" class="h-12 w-auto object-contain">
                <div class="h-8 w-[1px] bg-slate-200"></div>
                <img src="{{ asset('images/logo-cai47.png') }}" alt="CAI 47" class="h-8 w-auto object-contain">
            </div>

            <h1 class="font-heading text-xl font-extrabold text-slate-900">Login Admin</h1>
            <p class="text-xs text-slate-500 mt-1">ULUL ALBAB &bull; CAI 47 Focus Group Discussion</p>
        </div>

        <form method="POST" action="{{ route('admin.login.attempt') }}"
              class="glass-card rounded-3xl p-6 space-y-4 border border-emerald-200/80 shadow-md">
            @csrf

            @if ($errors->any())
                <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs px-3.5 py-2.5 font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label class="block text-xs font-extrabold text-slate-700 mb-1">Username / Email</label>
                <input type="text" name="email" value="{{ old('email', 'admin') }}" required autofocus
                       placeholder="admin"
                       class="w-full rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-500/10 text-xs font-bold py-2.5 px-3.5 bg-white">
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-500/10 text-xs font-bold py-2.5 px-3.5 bg-white">
            </div>

            <label class="flex items-center gap-2 text-xs font-medium text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                Ingat saya
            </label>

            <button type="submit"
                    class="w-full bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-heading font-extrabold text-xs py-3 rounded-xl shadow-md shadow-emerald-500/20 transition transform active:scale-95">
                Masuk Admin
            </button>
        </form>

        <p class="text-center text-xs font-semibold text-slate-400 mt-6">
            <a href="{{ route('home') }}" class="hover:text-emerald-700 transition">&larr; Kembali ke Form Notulensi</a>
        </p>
    </div>
</body>
</html>
