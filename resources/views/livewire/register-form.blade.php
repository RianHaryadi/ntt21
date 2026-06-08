<div class="min-h-screen flex">

    <!-- Left Panel - Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-ocean-950 via-ocean-900 to-ocean-800 flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-80 h-80 bg-sunset-500 rounded-full opacity-10 blur-3xl -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-ocean-500 rounded-full opacity-10 blur-3xl translate-y-1/2 -translate-x-1/4"></div>

        <!-- Logo -->
        <a href="/" class="flex items-center gap-3 relative z-10">
            <div class="w-11 h-11 rounded-2xl bg-sunset-500 flex items-center justify-center shadow-lg shadow-sunset-500/30">
                <i class="fas fa-compass text-white text-xl"></i>
            </div>
            <span class="text-2xl font-black text-white font-montserrat">Wonderful<span class="text-sunset-500">NTT</span></span>
        </a>

        <!-- Center content -->
        <div class="relative z-10">
            <div class="w-14 h-1 bg-sunset-500 rounded-full mb-6"></div>
            <h2 class="text-4xl font-black text-white font-montserrat leading-tight mb-4">
                Mulai<br>Petualangan<br><span class="text-sunset-500">Anda Hari Ini</span>
            </h2>
            <p class="text-gray-400 leading-relaxed text-sm max-w-xs">
                Buat akun gratis dan simpan rencana perjalanan, riwayat chat AI, serta booking Anda di satu tempat.
            </p>

            <div class="mt-8 space-y-3">
                @foreach(['Simpan riwayat chat dengan Ara', 'Pantau status booking hotel', 'Akses rekomendasi tersimpan'] as $feature)
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 rounded-full bg-sunset-500/20 border border-sunset-500/40 flex items-center justify-center shrink-0">
                        <i class="fas fa-check text-sunset-500 text-[9px]"></i>
                    </div>
                    <span class="text-gray-400 text-sm">{{ $feature }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <p class="text-gray-600 text-xs relative z-10">© {{ date('Y') }} Wonderful NTT</p>
    </div>

    <!-- Right Panel - Form -->
    <div class="flex-1 flex items-center justify-center px-6 py-12 bg-white overflow-y-auto">
        <div class="w-full max-w-md">

            <!-- Mobile Logo -->
            <div class="lg:hidden text-center mb-10">
                <a href="/" class="inline-flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-sunset-500 flex items-center justify-center shadow-lg shadow-sunset-500/30">
                        <i class="fas fa-compass text-white text-2xl"></i>
                    </div>
                    <span class="text-2xl font-black text-ocean-900 font-montserrat">Wonderful<span class="text-sunset-500">NTT</span></span>
                </a>
            </div>

            <h1 class="text-3xl font-black text-ocean-900 font-montserrat mb-1">Buat Akun</h1>
            <p class="text-gray-400 text-sm mb-8">Gratis — selesai dalam 30 detik</p>

            <form wire:submit.prevent="register" class="space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                        <input wire:model.lazy="name" type="text"
                            class="w-full pl-11 pr-4 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-900 text-sm font-medium focus:border-sunset-500 focus:bg-white focus:outline-none transition-all placeholder:text-gray-300"
                            placeholder="John Doe" />
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                        <input wire:model.lazy="email" type="email"
                            class="w-full pl-11 pr-4 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-900 text-sm font-medium focus:border-sunset-500 focus:bg-white focus:outline-none transition-all placeholder:text-gray-300"
                            placeholder="email@contoh.com" />
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                        <input wire:model.lazy="password" type="password" id="reg-password"
                            class="w-full pl-11 pr-12 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-900 text-sm font-medium focus:border-sunset-500 focus:bg-white focus:outline-none transition-all placeholder:text-gray-300"
                            placeholder="Min. 6 karakter" />
                        <button type="button" onclick="togglePwd('reg-password', 'eye2')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-600 transition focus:outline-none">
                            <i id="eye2" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                        <input wire:model.lazy="password_confirmation" type="password" id="reg-password-confirm"
                            class="w-full pl-11 pr-12 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-900 text-sm font-medium focus:border-sunset-500 focus:bg-white focus:outline-none transition-all placeholder:text-gray-300"
                            placeholder="Ulangi password" />
                        <button type="button" onclick="togglePwd('reg-password-confirm', 'eye3')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-600 transition focus:outline-none">
                            <i id="eye3" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-sunset-500 hover:bg-sunset-600 text-white text-sm font-bold tracking-wide transition-all shadow-lg shadow-sunset-500/25 hover:shadow-sunset-500/40 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 mt-2"
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

            <p class="mt-6 text-center text-sm text-gray-400">
                Sudah punya akun?
                <a href="{{ route('login', ['redirect' => session('login_redirect')]) }}"
                   class="text-sunset-500 font-bold hover:underline">Masuk di sini</a>
            </p>

            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-ocean-900 transition flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke beranda
                </a>
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
