{{--
    Partial: Review Section
    Variabel yang dibutuhkan:
    - $reviews      : collection of Review (dengan relasi ->user)
    - $reviewableType : 'destination' atau 'hotel'
    - $reviewableId   : id item
--}}
<section class="py-16 bg-paper border-t border-line">
<div class="max-w-4xl mx-auto px-4">

    <h2 class="text-2xl font-black font-serif tracking-tight text-ink mb-2">Ulasan Pengunjung</h2>

    {{-- Rating Summary --}}
    @if($reviews->count() > 0)
    @php
        $avg = round($reviews->avg('rating'), 1);
        $subRated = $reviews->filter(fn($r) => $r->hasSubRatings());
    @endphp
    <div class="flex flex-col sm:flex-row sm:items-center gap-6 mb-8">
        <div class="flex items-center gap-4">
            <div class="text-5xl font-black text-laut">{{ $avg }}</div>
            <div>
                <div class="flex gap-1 text-laut text-lg mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star{{ $i <= $avg ? '' : ($i - 0.5 <= $avg ? '-half-alt' : ' text-gray-300') }}"></i>
                    @endfor
                </div>
                <p class="text-sm text-muted">{{ $reviews->count() }} ulasan</p>
            </div>
        </div>

        @if($subRated->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:border-l sm:border-line sm:pl-6 flex-1">
            @foreach([
                'cleanliness_rating' => 'Kebersihan',
                'location_rating'    => 'Lokasi',
                'value_rating'       => 'Kesesuaian Harga',
                'service_rating'     => 'Layanan',
            ] as $field => $label)
            <div>
                <p class="text-[11px] text-muted uppercase tracking-wide mb-0.5">{{ $label }}</p>
                <p class="text-sm font-bold text-ink">{{ round($subRated->avg($field), 1) }}<span class="text-muted font-normal">/5</span></p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @else
    <p class="text-muted text-sm mb-6">Belum ada ulasan. Jadilah yang pertama!</p>
    @endif

    {{-- Daftar Ulasan --}}
    <div class="space-y-4 mb-10">
        @foreach($reviews->take(5) as $review)
        <div class="bg-surface rounded-2xl p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-petrol flex items-center justify-center text-white text-sm font-black flex-shrink-0">
                        {{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-ink">{{ $review->user->name ?? 'Anonim' }}</p>
                        <div class="flex gap-0.5 text-laut text-xs">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= $review->rating ? '' : ' text-gray-300' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-muted">{{ $review->created_at->diffForHumans() }}</span>
                    @auth
                        @if(auth()->id() === $review->user_id)
                        <form method="POST" action="{{ route('reviews.destroy', $review->id) }}" onsubmit="return confirm('Hapus ulasan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                        @endif
                    @endauth
                </div>
            </div>
            <p class="mt-3 text-sm text-muted leading-relaxed">{{ $review->body }}</p>
            @if($review->hasSubRatings())
            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-3 text-xs text-muted">
                <span>Kebersihan <span class="font-bold text-ink">{{ $review->cleanliness_rating }}/5</span></span>
                <span>Lokasi <span class="font-bold text-ink">{{ $review->location_rating }}/5</span></span>
                <span>Harga <span class="font-bold text-ink">{{ $review->value_rating }}/5</span></span>
                <span>Layanan <span class="font-bold text-ink">{{ $review->service_rating }}/5</span></span>
            </div>
            @endif
            @if($review->photo)
            <a href="{{ asset('storage/' . $review->photo) }}" target="_blank" rel="noopener" class="block mt-3 w-28 h-28 rounded-xl overflow-hidden border border-line">
                <img src="{{ asset('storage/' . $review->photo) }}" alt="Foto ulasan" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </a>
            @endif

            {{-- Helpful Vote --}}
            <div class="mt-4 pt-3 border-t border-line/60">
                @auth
                    @if(auth()->id() !== $review->user_id)
                    @php $markedHelpful = $review->isMarkedHelpfulBy(auth()->id()); @endphp
                    <form method="POST" action="{{ route('reviews.helpful', $review->id) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border transition-colors
                                {{ $markedHelpful ? 'bg-laut/10 border-laut/30 text-laut' : 'bg-transparent border-line text-muted hover:border-laut hover:text-laut' }}">
                            <i class="fas fa-thumbs-up"></i>
                            {{ $markedHelpful ? 'Membantu' : 'Apakah ini membantu?' }}
                            @if($review->helpful_votes_count > 0)
                            <span class="font-bold">({{ $review->helpful_votes_count }})</span>
                            @endif
                        </button>
                    </form>
                    @elseif($review->helpful_votes_count > 0)
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-muted">
                        <i class="fas fa-thumbs-up"></i> {{ $review->helpful_votes_count }} orang terbantu
                    </span>
                    @endif
                @else
                    @if($review->helpful_votes_count > 0)
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-muted">
                        <i class="fas fa-thumbs-up"></i> {{ $review->helpful_votes_count }} orang terbantu
                    </span>
                    @endif
                @endauth
            </div>
        </div>
        @endforeach
    </div>

    {{-- Form Tambah Ulasan --}}
    @auth
        @php $alreadyReviewed = $reviews->where('user_id', auth()->id())->count() > 0; @endphp
        @if($alreadyReviewed)
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 text-sm text-green-700 font-medium">
            <i class="fas fa-check-circle mr-2"></i> Anda sudah memberikan ulasan untuk ini.
        </div>
        @elseif(!($hasCompletedBooking ?? false))
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-center">
            <i class="fas fa-lock text-amber-500 mb-2"></i>
            <p class="text-sm text-amber-700 font-medium">Ulasan hanya bisa ditulis oleh tamu yang sudah booking dan menyelesaikan pembayaran di sini.</p>
        </div>
        @else
        <div class="bg-surface rounded-2xl p-6 border border-line">
            <h3 class="font-bold text-ink mb-4">Tulis Ulasan Anda</h3>

            @if(session('success')) <div class="text-green-600 text-sm mb-3 font-medium">{{ session('success') }}</div> @endif
            @if(session('error'))   <div class="text-red-500   text-sm mb-3 font-medium">{{ session('error') }}</div>   @endif

            <form method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="reviewable_type" value="{{ $reviewableType }}">
                <input type="hidden" name="reviewable_id"   value="{{ $reviewableId }}">

                {{-- Star Rating Input --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-ink mb-2">Rating Keseluruhan</label>
                    <div class="flex gap-2" id="star-rating">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button" data-val="{{ $i }}" onclick="setStarRating('rating', {{ $i }})"
                            class="star-btn-rating text-3xl text-gray-300 hover:text-laut transition cursor-pointer">
                            <i class="fas fa-star"></i>
                        </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="">
                    @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Sub-Rating Inputs --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    @foreach([
                        'cleanliness_rating' => 'Kebersihan',
                        'location_rating'    => 'Lokasi',
                        'value_rating'       => 'Kesesuaian Harga',
                        'service_rating'     => 'Layanan',
                    ] as $field => $label)
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1.5">{{ $label }}</label>
                        <div class="flex gap-1" id="star-{{ $field }}">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button" data-val="{{ $i }}" onclick="setStarRating('{{ $field }}', {{ $i }})"
                                class="star-btn-{{ $field }} text-lg text-gray-300 hover:text-laut transition cursor-pointer">
                                <i class="fas fa-star"></i>
                            </button>
                            @endfor
                        </div>
                        <input type="hidden" name="{{ $field }}" id="{{ $field }}-input" value="">
                        @error($field) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endforeach
                </div>

                {{-- Komentar --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-ink mb-2">Komentar</label>
                    <textarea name="body" rows="3" required maxlength="1000"
                        placeholder="Ceritakan pengalaman Anda..."
                        class="w-full border border-line rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-laut resize-none">{{ old('body') }}</textarea>
                    @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Foto (opsional) --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-ink mb-2">Foto <span class="font-normal text-muted">(opsional)</span></label>
                    <input type="file" name="photo" accept="image/*"
                        class="block w-full text-sm text-muted file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-laut/10 file:text-laut hover:file:bg-laut/20 file:cursor-pointer cursor-pointer">
                    @error('photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-primary py-2.5 px-6 text-sm">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Ulasan
                </button>
            </form>
        </div>
        @endif
    @else
    <div class="bg-petrol/5 rounded-2xl p-5 text-center">
        <p class="text-sm text-muted mb-3">Login untuk menulis ulasan</p>
        <a href="{{ route('login') }}" class="btn-primary py-2 px-5 text-sm">Login</a>
    </div>
    @endauth
</div>
</section>

@push('scripts')
<script>
function setStarRating(field, val) {
    document.getElementById(field + '-input').value = val;
    document.querySelectorAll('.star-btn-' + field).forEach((btn, i) => {
        btn.classList.toggle('text-laut', i < val);
        btn.classList.toggle('text-gray-300', i >= val);
    });
}
</script>
@endpush
