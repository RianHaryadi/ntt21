<div class="flex flex-col {{ $popup ? 'h-full' : 'h-screen max-w-4xl mx-auto px-4 py-6' }} justify-between">

    <div class="flex flex-col flex-1 {{ $popup ? 'h-full' : 'bg-ocean-900/40 backdrop-blur-xl border border-white/10 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.5)]' }} overflow-hidden">

        <!-- Header -->
        <div class="px-5 py-4 border-b border-white/10 bg-ocean-950/80 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-sunset-500 to-sunset-600 flex items-center justify-center text-white shadow-[0_0_12px_rgba(255,107,53,0.4)]">
                    <i class="fas fa-robot text-base animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-white flex items-center gap-2">
                        Ara <span class="text-[10px] bg-sunset-500 text-white font-semibold px-2 py-0.5 rounded-full uppercase tracking-widest">AI Guide</span>
                    </h1>
                    <p class="text-[11px] text-gray-400">Pencari Hidden Gem NTT</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <div class="hidden sm:flex items-center gap-1.5 bg-white/5 border border-white/15 px-2.5 py-1 rounded-xl text-[11px] text-gray-300">
                        <i class="fas fa-user-circle text-sunset-500 text-xs"></i>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                @else
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                       class="text-[11px] bg-white/5 hover:bg-white/10 border border-white/10 text-white px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                        <i class="fas fa-sign-in-alt text-sunset-500 text-xs"></i> Login
                    </a>
                @endauth

                @if($popup)
                    <button onclick="document.dispatchEvent(new CustomEvent('close-chat'))"
                        class="w-8 h-8 rounded-full bg-white/5 hover:bg-red-500/20 border border-white/10 hover:border-red-500/30 flex items-center justify-center text-gray-400 hover:text-red-400 transition-all text-xs">
                        <i class="fas fa-times"></i>
                    </button>
                @else
                    <a href="{{ route('home') }}"
                       class="w-8 h-8 rounded-full bg-white/5 hover:bg-sunset-500 border border-white/10 flex items-center justify-center text-white transition-all text-xs">
                        <i class="fas fa-home"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- Chat Messages Area -->
        <div class="flex-1 overflow-y-auto px-4 py-5 space-y-4" id="chat-area" style="scroll-behavior:smooth;">

            {{-- Static welcome message when session not started yet (popup lazy mode) --}}
            @if($messages->isEmpty())
                <div class="flex justify-start items-start gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-sunset-500 flex items-center justify-center text-white text-[10px] shrink-0 shadow-md">
                        <i class="fas fa-compass"></i>
                    </div>
                    <div class="max-w-[85%] px-4 py-3 rounded-2xl rounded-tl-sm bg-white/5 border border-white/10 text-gray-200 text-sm leading-relaxed backdrop-blur-md">
                        <div class="prose prose-invert max-w-none text-gray-100 text-sm">
                            Halo! Saya <strong class="text-sunset-400 font-bold">Ara</strong>, asisten perjalanan pribadi Anda khusus untuk menemukan destinasi tersembunyi (<em>hidden gems</em>) di Nusa Tenggara Timur (NTT). 🌴✨<br><br>
                            Wilayah mana di NTT yang ingin Anda kunjungi? (Flores, Sumba, Timor, Labuan Bajo, Rote, Alor, atau masih bingung?)
                        </div>
                    </div>
                </div>
            @endif

            @foreach ($messages as $message)
                <div class="flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }} items-start gap-2.5">

                    @if ($message->role !== 'user')
                        <div class="w-7 h-7 rounded-full bg-sunset-500 flex items-center justify-center text-white text-[10px] shrink-0 shadow-md">
                            <i class="fas fa-compass"></i>
                        </div>
                    @endif

                    <div class="max-w-[85%] px-4 py-3 rounded-2xl text-sm leading-relaxed shadow-soft
                        {{ $message->role === 'user'
                            ? 'bg-gradient-to-tr from-sunset-500 to-sunset-600 text-white rounded-tr-sm shadow-[0_4px_15px_rgba(255,107,53,0.2)]'
                            : 'bg-white/5 border border-white/10 text-gray-200 rounded-tl-sm backdrop-blur-md' }}">
                        <div class="prose prose-invert max-w-none text-[inherit] text-sm">
                            {!! nl2br(preg_replace('/\*\*(.*?)\*\*/', '<strong class="text-sunset-400 font-bold">$1</strong>', e($message->content))) !!}
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Typing indicator -->
            <div wire:loading wire:target="sendMessage" class="flex justify-start items-start gap-2.5">
                <div class="w-7 h-7 rounded-full bg-sunset-500 flex items-center justify-center text-white text-[10px] shrink-0">
                    <i class="fas fa-compass"></i>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl rounded-tl-sm px-4 py-3 shadow-soft">
                    <div class="flex gap-1.5 items-center h-4">
                        <span class="w-2 h-2 bg-sunset-500 rounded-full animate-bounce [animation-delay:0ms]"></span>
                        <span class="w-2 h-2 bg-sunset-500 rounded-full animate-bounce [animation-delay:150ms]"></span>
                        <span class="w-2 h-2 bg-sunset-500 rounded-full animate-bounce [animation-delay:300ms]"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="px-4 py-4 border-t border-white/10 bg-ocean-950/60 shrink-0">
            <form wire:submit="sendMessage" class="flex gap-3 items-center">
                <input
                    type="text"
                    wire:model="input"
                    placeholder="Ketik pesan Anda..."
                    class="flex-1 bg-white/5 border border-white/15 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-sunset-500 focus:border-sunset-500 transition-all placeholder:text-gray-500"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage"
                    autofocus
                    required
                />
                <button
                    type="submit"
                    class="bg-gradient-to-r from-sunset-500 to-sunset-600 hover:from-sunset-600 hover:to-sunset-500 text-white font-semibold px-4 py-3 rounded-xl text-sm transition-all shadow-[0_4px_15px_rgba(255,107,53,0.3)] hover:scale-105 active:scale-95 disabled:opacity-50 flex items-center gap-2 shrink-0"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage"
                >
                    <i class="fas fa-paper-plane text-xs"></i>
                </button>
            </form>

            @if($popup)
                <p class="text-center text-[10px] text-gray-600 mt-2">
                    Setelah selesai, Ara akan memberikan rekomendasi lengkap untuk Anda
                </p>
            @endif
        </div>
    </div>

    <!-- Auto-scroll script -->
    <script>
        (function() {
            const scrollChat = () => {
                const el = document.getElementById('chat-area');
                if (el) el.scrollTop = el.scrollHeight;
            };
            document.addEventListener('DOMContentLoaded', scrollChat);
            document.addEventListener('livewire:navigated', scrollChat);
            document.addEventListener('scroll-chat', () => setTimeout(scrollChat, 80));
            document.addEventListener('livewire:update', () => setTimeout(scrollChat, 80));
        })();
    </script>
</div>
