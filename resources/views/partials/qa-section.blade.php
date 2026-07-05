{{--
    Partial: Community Q&A Section
    Variabel yang dibutuhkan:
    - $questions       : collection of Question (dengan relasi ->user, ->answers.user)
    - $questionableType : 'destination' atau 'hotel'
    - $questionableId   : id item
--}}
<section class="py-16 bg-surface border-t border-line">
<div class="max-w-4xl mx-auto px-4">

    <h2 class="text-2xl font-black font-serif tracking-tight text-ink mb-2 flex items-center gap-2">
        <i class="fas fa-comments text-clay text-xl"></i> Tanya Jawab Komunitas
    </h2>
    <p class="text-muted text-sm mb-8">Punya pertanyaan sebelum booking? Tanyakan pada komunitas Pesona NTT.</p>

    {{-- Ask a Question --}}
    @auth
    <div class="bg-paper rounded-2xl p-6 border border-line mb-10">
        <h3 class="font-bold text-ink mb-4">Ajukan Pertanyaan</h3>

        @if(session('success')) <div class="text-green-600 text-sm mb-3 font-medium">{{ session('success') }}</div> @endif
        @if(session('error'))   <div class="text-red-500 text-sm mb-3 font-medium">{{ session('error') }}</div> @endif

        <form method="POST" action="{{ route('questions.store') }}">
            @csrf
            <input type="hidden" name="questionable_type" value="{{ $questionableType }}">
            <input type="hidden" name="questionable_id" value="{{ $questionableId }}">
            <textarea name="body" rows="3" required maxlength="1000"
                placeholder="Contoh: Apakah tempat ini cocok untuk anak-anak?"
                class="w-full border border-line rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-laut resize-none mb-3">{{ old('body') }}</textarea>
            @error('body') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror
            <button type="submit" class="btn-primary py-2.5 px-6 text-sm">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Pertanyaan
            </button>
        </form>
    </div>
    @else
    <div class="bg-petrol/5 rounded-2xl p-5 text-center mb-10">
        <p class="text-sm text-muted mb-3">Login untuk mengajukan pertanyaan</p>
        <a href="{{ route('login') }}" class="btn-primary py-2 px-5 text-sm">Login</a>
    </div>
    @endauth

    {{-- Question List --}}
    <div class="space-y-5">
        @forelse($questions as $question)
        <div class="bg-paper rounded-2xl p-5 border border-line">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-petrol flex items-center justify-center text-white text-sm font-black flex-shrink-0">
                        {{ strtoupper(substr($question->user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-ink">{{ $question->user->name ?? 'Anonim' }}</p>
                        <p class="text-xs text-muted">{{ $question->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @auth
                    @if(auth()->id() === $question->user_id)
                    <form method="POST" action="{{ route('questions.destroy', $question->id) }}" onsubmit="return confirm('Hapus pertanyaan ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                    @endif
                @endauth
            </div>

            <p class="text-sm text-ink font-medium leading-relaxed mb-4">{{ $question->body }}</p>

            {{-- Answers --}}
            @if($question->answers->count() > 0)
            <div class="space-y-3 pl-4 border-l-2 border-line mb-4">
                @foreach($question->answers as $answer)
                <div class="bg-surface rounded-xl p-4">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-clay flex items-center justify-center text-white text-xs font-black flex-shrink-0">
                                {{ strtoupper(substr($answer->user->name ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-xs text-ink">{{ $answer->user->name ?? 'Anonim' }}</p>
                                <p class="text-[11px] text-muted">{{ $answer->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @auth
                            @if(auth()->id() === $answer->user_id)
                            <form method="POST" action="{{ route('answers.destroy', $answer->id) }}" onsubmit="return confirm('Hapus jawaban ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endif
                        @endauth
                    </div>
                    <p class="text-sm text-muted leading-relaxed">{{ $answer->body }}</p>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Answer Form --}}
            @auth
            <details class="group">
                <summary class="text-xs font-bold text-laut cursor-pointer select-none list-none flex items-center gap-1.5">
                    <i class="fas fa-reply"></i> Jawab pertanyaan ini
                </summary>
                <form method="POST" action="{{ route('questions.answers.store', $question->id) }}" class="mt-3 flex gap-2">
                    @csrf
                    <input type="text" name="body" required maxlength="1000" placeholder="Tulis jawaban Anda..."
                        class="flex-1 border border-line rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-laut">
                    <button type="submit" class="bg-laut text-white text-sm font-bold px-4 rounded-xl hover:bg-laut/90 transition-colors">
                        Kirim
                    </button>
                </form>
            </details>
            @endauth
        </div>
        @empty
        <p class="text-muted text-sm text-center py-8">Belum ada pertanyaan. Jadilah yang pertama bertanya!</p>
        @endforelse
    </div>
</div>
</section>
