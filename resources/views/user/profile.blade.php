@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="pt-28 pb-20 bg-paper min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-10">
            <a href="{{ route('dashboard') }}" class="text-xs text-muted hover:text-ink transition-colors flex items-center gap-1.5 mb-4">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-bold text-ink font-serif tracking-tight">Profil Saya</h1>
            <p class="text-muted text-sm mt-1">Kelola informasi akun dan keamanan Anda.</p>
        </div>

        @if(session('success'))
        <div class="mb-8 bg-clay/10 border border-clay/25 text-clay px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <div class="space-y-8">

            <!-- Avatar -->
            <div class="bg-surface rounded-2xl p-6 shadow-sm border border-line">
                <h2 class="font-bold text-ink font-serif mb-4">Foto Profil</h2>
                <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="flex items-center gap-5">
                    @csrf
                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gradient-to-tr from-clay to-ink flex items-center justify-center text-paper text-xl font-bold shadow-sm shrink-0">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="avatar" accept="image/*"
                               class="block w-full text-sm text-muted file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-clay/10 file:text-clay hover:file:bg-clay/20 file:cursor-pointer cursor-pointer">
                        @error('avatar')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-muted mt-1.5">JPG/PNG, maks 2MB.</p>
                    </div>
                    <button type="submit" class="btn-primary py-2.5 px-5 text-sm shrink-0">Unggah</button>
                </form>
            </div>

            <!-- Informasi Akun -->
            <div class="bg-surface rounded-2xl p-6 shadow-sm border border-line">
                <h2 class="font-bold text-ink font-serif mb-4">Informasi Akun</h2>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-3 rounded-xl border border-line bg-paper text-ink text-sm font-semibold focus:border-clay focus:outline-none transition-all">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-3 rounded-xl border border-line bg-paper text-ink text-sm font-semibold focus:border-clay focus:outline-none transition-all">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="btn-primary py-2.5 px-6 text-sm">Simpan Perubahan</button>
                </form>
            </div>

            <!-- Ganti Password -->
            <div class="bg-surface rounded-2xl p-6 shadow-sm border border-line">
                <h2 class="font-bold text-ink font-serif mb-1">
                    {{ $user->password ? 'Ganti Password' : 'Buat Password' }}
                </h2>
                <p class="text-muted text-xs mb-4">
                    @if(!$user->password)
                        Akun Anda dibuat via Google — buat password agar bisa login manual juga.
                    @else
                        Gunakan password yang kuat dan tidak dipakai di tempat lain.
                    @endif
                </p>
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @if($user->password)
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">Password Saat Ini</label>
                        <input type="password" name="current_password"
                               class="w-full px-4 py-3 rounded-xl border border-line bg-paper text-ink text-sm font-semibold focus:border-clay focus:outline-none transition-all">
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">Password Baru</label>
                        <input type="password" name="password"
                               class="w-full px-4 py-3 rounded-xl border border-line bg-paper text-ink text-sm font-semibold focus:border-clay focus:outline-none transition-all"
                               placeholder="Min. 6 karakter">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-4 py-3 rounded-xl border border-line bg-paper text-ink text-sm font-semibold focus:border-clay focus:outline-none transition-all">
                    </div>
                    <button type="submit" class="btn-primary py-2.5 px-6 text-sm">
                        {{ $user->password ? 'Ubah Password' : 'Buat Password' }}
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
