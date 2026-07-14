<div class="min-h-screen flex relative z-10">

    <!-- Left Panel - Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-[#f5eae2] flex-col justify-between p-12 relative overflow-hidden border-r border-slate-200/60">
        <!-- Decorative blobs -->
        <div class="absolute top-0 right-0 w-80 h-80 bg-laut rounded-full opacity-10 blur-3xl -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#a3b899] rounded-full opacity-20 blur-3xl translate-y-1/2 -translate-x-1/4"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-laut rounded-full opacity-5 blur-3xl -translate-x-1/2 -translate-y-1/2"></div>

        <!-- Logo -->
        <a href="/" class="flex items-center gap-3 relative z-10">
            <div class="w-11 h-11 rounded-2xl bg-laut flex items-center justify-center shadow-md shadow-laut/20">
                <i class="fas fa-compass text-white text-xl"></i>
            </div>
            <span class="text-2xl font-black text-slate-800 font-serif tracking-tight">Pesona<span class="text-laut">NTT</span></span>
        </a>

        <!-- Center content -->
        <div class="relative z-10">
            <div class="w-14 h-1 bg-laut rounded-full mb-6"></div>
            <h2 class="text-4xl font-black text-slate-800 font-serif tracking-tight leading-tight mb-4">
                Jelajahi<br>Keindahan<br><span class="text-laut">Nusa Tenggara</span>
            </h2>
            <p class="text-slate-600 leading-relaxed text-sm max-w-xs">
                Temukan destinasi tersembunyi, hotel terbaik, dan paket tour eksklusif di NTT bersama AI Guide kami.
            </p>

            <!-- Feature list -->
            <div class="mt-8 space-y-3">
                @foreach(['AI Guide Ara — rekomendasi personal', 'Destinasi tersembunyi NTT', 'Booking hotel & paket tour'] as $feature)
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 rounded-full bg-laut/10 border border-laut/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-check text-laut text-[9px]"></i>
                    </div>
                    <span class="text-slate-600 text-sm font-bold">{{ $feature }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Bottom -->
        <p class="text-slate-500 text-xs relative z-10 font-bold">© {{ date('Y') }} Pesona NTT</p>
    </div>

    <!-- Right Panel - Form -->
    <div class="flex-1 overflow-y-auto bg-transparent">
        <div class="min-h-full flex flex-col justify-center items-center px-6 py-8 lg:py-12">
            <div class="w-full max-w-md bg-white border border-slate-200/60 p-8 lg:p-10 rounded-[32px] shadow-2xl">

            <!-- Mobile Logo -->
            <div class="lg:hidden text-center mb-10">
                <a href="/" class="inline-flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-laut flex items-center justify-center shadow-lg shadow-laut/30">
                        <i class="fas fa-compass text-white text-2xl"></i>
                    </div>
                    <span class="text-2xl font-black text-slate-800 font-serif tracking-tight">Pesona<span class="text-laut">NTT</span></span>
                </a>
            </div>

            <h1 class="text-3xl font-black text-slate-800 font-serif tracking-tight mb-1">Selamat Datang</h1>
            <p class="text-slate-500 text-sm mb-8">Masuk ke akun Anda untuk melanjutkan</p>

            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div class="mb-6 bg-emerald-50 border border-emerald-500/25 p-4 rounded-2xl flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                    <p class="text-emerald-600 text-sm font-medium">{{ session('message') }}</p>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="mb-6 bg-red-500/10 border border-red-500/25 p-4 rounded-2xl flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <p class="text-red-500 text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <form wire:submit.prevent="login" class="space-y-5">
                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input wire:model.lazy="email" type="email"
                            class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm font-semibold focus:border-laut focus:bg-white focus:outline-none transition-all placeholder:text-slate-400"
                            placeholder="email@contoh.com" />
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Password</label>
                        <a href="#" class="text-xs text-laut font-semibold hover:underline">Lupa password?</a>
                    </div>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input wire:model.lazy="password" type="password" id="login-password"
                            class="w-full pl-11 pr-12 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm font-semibold focus:border-laut focus:bg-white focus:outline-none transition-all placeholder:text-slate-400"
                            placeholder="••••••••" />
                        <button type="button" onclick="togglePwd('login-password', 'eye1')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition focus:outline-none">
                            <i id="eye1" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-laut to-petrol hover:from-petrol hover:to-laut text-white text-sm font-black tracking-wide transition-all shadow-sm shadow-laut/10 hover:shadow-laut/20 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 cursor-pointer"
                    wire:loading.attr="disabled" wire:target="login">
                    <span wire:loading.remove wire:target="login">Masuk <i class="fas fa-arrow-right ml-1 text-xs"></i></span>
                    <span wire:loading wire:target="login" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </form>

            <!-- Divider OR -->
            <div class="mt-6 flex items-center gap-4">
                <div class="flex-1 h-px bg-slate-100"></div>
                <span class="text-xs text-slate-500 font-medium">atau</span>
                <div class="flex-1 h-px bg-slate-100"></div>
            </div>

            <!-- Google Login -->
            <a href="{{ route('auth.google') }}"
               class="mt-4 w-full py-3.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 text-sm font-semibold flex items-center justify-center gap-3 transition-all hover:shadow-sm">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Masuk dengan Google
            </a>

            <p class="mt-6 text-center text-sm text-slate-500">
                Belum punya akun?
                <a href="{{ route('register', ['redirect' => session('login_redirect') ?? request()->query('redirect')]) }}"
                   class="text-laut font-bold hover:underline">Daftar sekarang</a>
            </p>

            <!-- Divider -->
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <a href="{{ route('home') }}" class="text-xs text-slate-500 hover:text-slate-700 transition flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke beranda
                </a>
            </div>
        </div>
    </div>
</div>
</div>

<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash text-sm';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye text-sm';
    }
}
</script>
