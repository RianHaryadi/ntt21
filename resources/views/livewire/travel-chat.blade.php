<div class="flex flex-col {{ $popup ? 'h-full' : 'h-screen max-w-4xl mx-auto px-4 py-6' }} justify-between">

    <div class="flex flex-col flex-1 {{ $popup ? 'h-full' : 'bg-ink/40 backdrop-blur-xl border border-white/10 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.5)]' }} overflow-hidden">

        <!-- Header -->
        <div class="px-5 py-4 border-b border-white/10 bg-ink/80 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-clay to-ink flex items-center justify-center text-white shadow-[0_0_12px_rgba(15,110,99,0.4)]">
                    <i class="fas fa-robot text-base animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-white flex items-center gap-2">
                        Ara <span class="text-[10px] bg-clay text-white font-semibold px-2 py-0.5 rounded-full uppercase tracking-widest">AI Guide</span>
                    </h1>
                    <p class="text-[11px] text-gray-400">Pencari Hidden Gem NTT</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <div class="hidden sm:flex items-center gap-1.5 bg-paper/5 border border-white/15 px-2.5 py-1 rounded-xl text-[11px] text-gray-300">
                        <i class="fas fa-user-circle text-clay text-xs"></i>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                @else
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                       class="text-[11px] bg-paper/5 hover:bg-paper/10 border border-white/10 text-white px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                        <i class="fas fa-sign-in-alt text-clay text-xs"></i> Login
                    </a>
                @endauth

                <a href="https://wa.me/{{ config('services.support.whatsapp') }}?text={{ urlencode('Halo, saya butuh bantuan CS terkait booking di Pesona NTT.') }}"
                   target="_blank" rel="noopener" title="Butuh bantuan manusia? Chat Admin"
                   class="w-8 h-8 rounded-full bg-paper/5 hover:bg-emerald-500/20 border border-white/10 hover:border-emerald-500/30 flex items-center justify-center text-gray-400 hover:text-emerald-400 transition-all text-xs">
                    <i class="fab fa-whatsapp"></i>
                </a>

                @if($popup)
                    <button onclick="document.dispatchEvent(new CustomEvent('close-chat'))"
                        class="w-8 h-8 rounded-full bg-paper/5 hover:bg-red-500/20 border border-white/10 hover:border-red-500/30 flex items-center justify-center text-gray-400 hover:text-red-400 transition-all text-xs">
                        <i class="fas fa-times"></i>
                    </button>
                @else
                    <a href="{{ route('home') }}"
                       class="w-8 h-8 rounded-full bg-paper/5 hover:bg-clay border border-white/10 flex items-center justify-center text-white transition-all text-xs">
                        <i class="fas fa-home"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- Chat Messages Area -->
        <div class="flex-1 overflow-y-auto px-4 py-5 flex flex-col gap-4" id="chat-area" style="scroll-behavior:smooth;">

            {{-- Static welcome message when session not started yet (popup lazy mode) --}}
            @if($messages->isEmpty())
                <div class="flex justify-start items-start gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-clay flex items-center justify-center text-white text-[10px] shrink-0 shadow-md">
                        <i class="fas fa-compass"></i>
                    </div>
                    <div class="max-w-[85%] px-4 py-3 rounded-2xl rounded-tl-sm bg-paper/5 border border-white/10 text-gray-200 text-sm leading-relaxed backdrop-blur-md">
                        <div class="prose prose-invert max-w-none text-gray-100 text-sm">
                            Halo! Saya <strong class="text-clay font-bold">Ara</strong>, asisten perjalanan pribadi Anda khusus untuk menemukan destinasi tersembunyi (<em>hidden gems</em>) di Nusa Tenggara Timur (NTT). 🌴✨<br><br>
                            Wilayah mana di NTT yang ingin Anda kunjungi? (Flores, Sumba, Timor, Labuan Bajo, Rote, Alor, atau masih bingung?)
                        </div>
                    </div>
                </div>
            @endif

            @foreach ($messages as $message)
                <div wire:key="chat-msg-{{ $message->id }}" class="flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }} items-start gap-2.5">

                    @if ($message->role !== 'user')
                        <div class="w-7 h-7 rounded-full bg-clay flex items-center justify-center text-white text-[10px] shrink-0 shadow-md">
                            <i class="fas fa-compass"></i>
                        </div>
                    @endif

                    <div class="max-w-[85%] px-4 py-3 rounded-2xl text-sm leading-relaxed shadow-soft
                        {{ $message->role === 'user'
                            ? 'bg-gradient-to-tr from-clay to-ink text-white rounded-tr-sm shadow-[0_4px_15px_rgba(15,110,99,0.2)]'
                            : 'bg-paper/5 border border-white/10 text-gray-200 rounded-tl-sm backdrop-blur-md' }}">
                        <div class="prose prose-invert max-w-none text-[inherit] text-sm">
                            {!! nl2br(preg_replace('/\*\*(.*?)\*\*/', '<strong class="text-clay font-bold">$1</strong>', e($message->content))) !!}
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Typing indicator (dengan status berganti agar tak terasa nge-freeze) -->
            <div wire:loading wire:target="sendMessage" id="ara-typing" class="flex justify-start items-start gap-2.5 order-last">
                <div class="w-7 h-7 rounded-full bg-clay flex items-center justify-center text-white text-[10px] shrink-0">
                    <i class="fas fa-compass"></i>
                </div>
                <div class="bg-paper/5 border border-white/10 rounded-2xl rounded-tl-sm px-4 py-3 shadow-soft"
                     x-data="{
                         msgs: ['Ara sedang berpikir…','Menelusuri hidden gems NTT…','Mencocokkan dengan preferensimu…','Menyusun rekomendasi terbaik…','Hampir selesai…'],
                         i: 0, msg: 'Ara sedang berpikir…', timer: null,
                         start() {
                             this.i = 0; this.msg = this.msgs[0];
                             clearInterval(this.timer);
                             this.timer = setInterval(() => { this.i = (this.i + 1) % this.msgs.length; this.msg = this.msgs[this.i]; }, 2600);
                         },
                         init() { this.start(); }
                     }"
                     x-on:ara-thinking-start.window="start()">
                    <div class="flex items-center gap-2.5 h-4">
                        <div class="flex gap-1.5 items-center">
                            <span class="w-2 h-2 bg-clay rounded-full animate-bounce [animation-delay:0ms]"></span>
                            <span class="w-2 h-2 bg-clay rounded-full animate-bounce [animation-delay:150ms]"></span>
                            <span class="w-2 h-2 bg-clay rounded-full animate-bounce [animation-delay:300ms]"></span>
                        </div>
                        <span class="text-[11px] text-gray-400 font-medium whitespace-nowrap" x-text="msg"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="px-4 py-4 border-t border-white/10 bg-ink/60 shrink-0">
            <form wire:submit="sendMessage" class="flex gap-3 items-center">
                <input
                    type="text"
                    wire:model="input"
                    placeholder="Ketik pesan Anda..."
                    class="flex-1 bg-paper/5 border border-white/15 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-clay focus:border-clay transition-all placeholder:text-muted"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage"
                    autofocus
                    required
                />
                <button
                    type="submit"
                    class="bg-gradient-to-r from-clay to-ink hover:from-ink hover:to-clay text-white font-semibold px-4 py-3 rounded-xl text-sm transition-all shadow-[0_4px_15px_rgba(15,110,99,0.3)] hover:scale-105 active:scale-95 disabled:opacity-50 flex items-center gap-2 shrink-0"
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

    <!-- Auto-scroll + Optimistic UI -->
    <script>
        (function() {
            const scrollChat = () => {
                const el = document.getElementById('chat-area');
                if (el) el.scrollTop = el.scrollHeight;
            };

            function initChat() {
                const chatArea = document.getElementById('chat-area');
                const form = document.querySelector('form[wire\\:submit]');
                if (!chatArea || !form) return;

                // Hapus listener lama jika ada
                form.removeEventListener('submit', form._optimisticHandler);

                form._optimisticHandler = function() {
                    const input = form.querySelector('input[wire\\:model="input"]');
                    const msg = (input?.value || '').trim();
                    if (!msg) return;

                    // Tambah bubble user langsung (optimistic)
                    const div = document.createElement('div');
                    div.className = 'flex justify-end items-start gap-2.5 optimistic-bubble';
                    const safe = msg.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
                    div.innerHTML = `<div class="max-w-[85%] px-4 py-3 rounded-2xl text-sm leading-relaxed bg-gradient-to-tr from-clay to-ink text-white rounded-tr-sm shadow-[0_4px_15px_rgba(15,110,99,0.2)]"><div class="prose prose-invert max-w-none text-inherit text-sm">${safe}</div></div>`;

                    // Tambahkan di akhir (aman untuk morph Livewire). Posisi visual di atas
                    // indikator "..." dijamin oleh CSS order-last pada #ara-typing.
                    chatArea.appendChild(div);
                    chatArea.scrollTop = chatArea.scrollHeight;

                    // Kosongkan input langsung (visual, sebelum Livewire selesai)
                    input.value = '';

                    // Reset status "Ara sedang berpikir…" agar mulai dari awal tiap kirim.
                    window.dispatchEvent(new CustomEvent('ara-thinking-start'));
                };

                form.addEventListener('submit', form._optimisticHandler);
            }

            // Hapus optimistic bubble saat Livewire selesai render (data asli sudah ada)
            document.addEventListener('livewire:update', () => {
                document.querySelectorAll('.optimistic-bubble').forEach(el => el.remove());
                setTimeout(scrollChat, 80);
            });

            document.addEventListener('DOMContentLoaded', () => { scrollChat(); initChat(); });
            document.addEventListener('livewire:navigated', () => { scrollChat(); initChat(); });
            document.addEventListener('scroll-chat', () => setTimeout(scrollChat, 80));
        })();
    </script>
</div>
