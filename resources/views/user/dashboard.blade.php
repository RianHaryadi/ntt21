@extends('layouts.app')

@section('title', 'Dashboard Saya')

@section('content')
<div class="pt-28 pb-20 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-10 flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-ocean-800 to-ocean-600 flex items-center justify-center text-white text-2xl font-black shadow-lg">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-3xl font-black text-ocean-900 font-montserrat">Halo, {{ $user->name }}!</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-2xl p-6 shadow-soft border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-ocean-600 text-xl">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-ocean-900">{{ $chatSessions->count() }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Sesi AI Chat</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-soft border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-sunset-500 text-xl">
                    <i class="fas fa-hotel"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-ocean-900">{{ $hotelBookings->count() }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Booking Hotel</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-soft border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-ocean-900">{{ $hotelBookings->where('status', 'approve')->count() }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Booking Dikonfirmasi</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Riwayat AI Chat -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-ocean-900 flex items-center justify-center">
                            <i class="fas fa-robot text-sunset-500 text-sm"></i>
                        </div>
                        <h2 class="font-bold text-ocean-900 font-montserrat">Riwayat Chat dengan Ara</h2>
                    </div>
                    <a href="{{ route('travel.chat') }}" class="text-xs text-sunset-500 font-bold hover:underline flex items-center gap-1">
                        <i class="fas fa-plus"></i> Chat Baru
                    </a>
                </div>

                @if($chatSessions->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comment-slash text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-gray-400 text-sm">Belum ada sesi chat.</p>
                        <a href="{{ route('travel.chat') }}" class="mt-4 inline-block text-sm text-sunset-500 font-bold hover:underline">
                            Mulai chat dengan Ara →
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($chatSessions as $session)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $session->status === 'completed' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                                        <i class="fas {{ $session->status === 'completed' ? 'fa-check' : 'fa-clock' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">
                                            Sesi {{ $session->created_at->format('d M Y') }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $session->messages_count }} pesan ·
                                            <span class="{{ $session->status === 'completed' ? 'text-green-500' : 'text-blue-500' }} font-semibold">
                                                {{ $session->status === 'completed' ? 'Selesai' : 'Aktif' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    @if($session->status === 'completed')
                                        <a href="{{ route('travel.recommendation', $session->session_token) }}"
                                           class="text-xs bg-sunset-500 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-sunset-600 transition">
                                            Lihat
                                        </a>
                                    @else
                                        <a href="{{ route('travel.chat', ['token' => $session->session_token]) }}"
                                           class="text-xs bg-ocean-900 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-ocean-800 transition">
                                            Lanjut
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Riwayat Booking Hotel -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-sunset-500 flex items-center justify-center">
                            <i class="fas fa-hotel text-white text-sm"></i>
                        </div>
                        <h2 class="font-bold text-ocean-900 font-montserrat">Riwayat Booking Hotel</h2>
                    </div>
                    <a href="{{ route('hotels.index') }}" class="text-xs text-sunset-500 font-bold hover:underline flex items-center gap-1">
                        <i class="fas fa-search"></i> Cari Hotel
                    </a>
                </div>

                @if($hotelBookings->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-hotel text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-gray-400 text-sm">Belum ada booking hotel.</p>
                        <a href="{{ route('hotels.index') }}" class="mt-4 inline-block text-sm text-sunset-500 font-bold hover:underline">
                            Jelajahi hotel →
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($hotelBookings as $booking)
                            <div class="px-6 py-4 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $booking->hotel->name ?? 'Hotel' }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $booking->check_in_date?->format('d M') }} – {{ $booking->check_out_date?->format('d M Y') }}
                                            · {{ ucfirst($booking->room_type) }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5 font-mono">{{ $booking->booking_number }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-block text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-full
                                            {{ $booking->status === 'approve' ? 'bg-green-100 text-green-700' :
                                               ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            {{ $booking->status === 'approve' ? 'Dikonfirmasi' : ($booking->status === 'pending' ? 'Menunggu' : 'Dibatalkan') }}
                                        </span>
                                        <p class="text-xs font-bold text-ocean-900 mt-1.5">
                                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-gradient-to-r from-ocean-900 to-ocean-800 rounded-2xl p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div>
                <h3 class="text-white font-bold text-lg font-montserrat">Rencanakan Perjalanan Berikutnya</h3>
                <p class="text-gray-400 text-sm mt-1">Chat dengan Ara, AI Guide NTT, dan temukan destinasi tersembunyi!</p>
            </div>
            <a href="{{ route('travel.chat') }}"
               class="shrink-0 bg-sunset-500 hover:bg-sunset-600 text-white font-bold px-6 py-3 rounded-xl transition flex items-center gap-2 text-sm">
                <i class="fas fa-robot"></i> Chat dengan Ara
            </a>
        </div>

    </div>
</div>
@endsection
