<div class="min-h-screen flex relative z-10">

    <!-- Left Panel - Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-[#f5eae2] flex-col justify-between p-12 relative overflow-hidden border-r border-slate-200/60">
        <div class="absolute top-0 right-0 w-80 h-80 bg-laut rounded-full opacity-10 blur-3xl -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#a3b899] rounded-full opacity-20 blur-3xl translate-y-1/2 -translate-x-1/4"></div>

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
                Mulai<br>Petualangan<br><span class="text-laut">Anda Hari Ini</span>
            </h2>
            <p class="text-slate-600 leading-relaxed text-sm max-w-xs">
                Buat akun gratis dan simpan rencana perjalanan, riwayat chat AI, serta booking Anda di satu tempat.
            </p>

            <div class="mt-8 space-y-3">
                @foreach(['Simpan riwayat chat dengan Ara', 'Pantau status booking hotel', 'Akses rekomendasi tersimpan'] as $feature)
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 rounded-full bg-laut/10 border border-laut/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-check text-laut text-[9px]"></i>
                    </div>
                    <span class="text-slate-600 text-sm font-bold">{{ $feature }}</span>
                </div>
                @endforeach
            </div>
        </div>

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

            <h1 class="text-3xl font-black text-slate-800 font-serif tracking-tight mb-1">Buat Akun</h1>
            <p class="text-slate-500 text-sm mb-8">Gratis — selesai dalam 30 detik</p>

            <form wire:submit.prevent="register" class="space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input wire:model.lazy="name" type="text"
                            class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm font-semibold focus:border-laut focus:bg-white focus:outline-none transition-all placeholder:text-slate-400"
                            placeholder="John Doe" />
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

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
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input wire:model.lazy="password" type="password" id="reg-password"
                            class="w-full pl-11 pr-12 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm font-semibold focus:border-laut focus:bg-white focus:outline-none transition-all placeholder:text-slate-400"
                            placeholder="Min. 6 karakter" />
                        <button type="button" onclick="togglePwd('reg-password', 'eye2')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition focus:outline-none">
                            <i id="eye2" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input wire:model.lazy="password_confirmation" type="password" id="reg-password-confirm"
                            class="w-full pl-11 pr-12 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm font-semibold focus:border-laut focus:bg-white focus:outline-none transition-all placeholder:text-slate-400"
                            placeholder="Ulangi password" />
                        <button type="button" onclick="togglePwd('reg-password-confirm', 'eye3')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition focus:outline-none">
                            <i id="eye3" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Referral Code (optional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Kode Referral <span class="normal-case font-medium text-slate-400">(opsional)</span></label>
                    <div class="relative">
                        <i class="fas fa-gift absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input wire:model.lazy="referral_code" type="text"
                            class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm font-semibold uppercase focus:border-laut focus:bg-white focus:outline-none transition-all placeholder:text-slate-400 placeholder:normal-case"
                            placeholder="Punya kode teman?" />
                    </div>
                    @error('referral_code')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-laut to-petrol hover:from-petrol hover:to-laut text-white text-sm font-black tracking-wide transition-all shadow-sm shadow-laut/10 hover:shadow-laut/20 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 mt-2 cursor-pointer"
                    wire:loading.attr="disabled" wire:target="register">
                    <span wire:loading.remove wire:target="register">Buat Akun <i class="fas fa-arrow-right ml-1 text-xs"></i></span>
                    <span wire:loading wire:target="register" class="flex items-center gap-2">
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

            <!-- Google Register -->
            <a href="{{ route('auth.google') }}"
               class="mt-4 w-full py-3.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 text-sm font-semibold flex items-center justify-center gap-3 transition-all hover:shadow-sm">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Daftar dengan Google
            </a>

            <p class="mt-6 text-center text-sm text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login', ['redirect' => session('login_redirect')]) }}"
                   class="text-laut font-bold hover:underline">Masuk di sini</a>
            </p>

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
