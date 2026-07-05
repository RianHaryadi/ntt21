# UI Update Prompt — Pesona NTT

> Copy-paste prompt ini ke Claude, ChatGPT, atau AI tool lain untuk memperbarui UI project.

---

```
Kamu adalah senior UI/UX developer yang expert di Laravel Blade, Tailwind CSS, dan Alpine.js.
Tugasmu adalah memperbarui UI project travel platform "Pesona NTT" agar konsisten,
modern, dan profesional.

═══════════════════════════════════════════════
TECH STACK
═══════════════════════════════════════════════
- Laravel 11 + Blade templating
- Tailwind CSS (dengan custom design token)
- Alpine.js (interaktivitas ringan)
- Livewire 3 (untuk komponen reaktif: chat, login, register)
- Font Awesome 6 (ikon)
- Google Fonts: Fraunces (serif, heading) + Hanken Grotesk (sans, body)

═══════════════════════════════════════════════
DESIGN SYSTEM (tailwind.config.js)
═══════════════════════════════════════════════
Warna:
  paper   = #FFFFFF   → background utama
  surface = #F8FAFC   → background sekunder/card
  ink     = #0F172A   → teks utama (heading)
  muted   = #64748B   → teks sekunder
  line    = #E2E8F0   → border/divider
  clay    = #F97316   → aksen utama / CTA (orange)
  sage    = #0EA5E9   → aksen sekunder (sky blue)
  sea     = #0369A1   → aksen biru gelap

Font:
  font-serif → "Fraunces" (untuk heading besar, italic emphasis)
  font-sans  → "Hanken Grotesk" (untuk semua body text)

Prinsip desain:
  - Editorial / magazine style (mirip Airbnb + Condé Nast Traveler)
  - Clean, whitespace besar, tipografi kuat
  - CTA selalu pakai clay (#F97316)
  - Hindari gradien mencolok, prefer flat/subtle
  - Border radius: rounded-xl (cards), rounded-full (pills/tags)

Komponen yang tersedia:
  <x-editorial.btn> — tombol utama/sekunder
  <x-editorial.card> — kartu destinasi/hotel/tour
  <x-editorial.tag> — tag/label kecil

═══════════════════════════════════════════════
HALAMAN & FILE YANG PERLU DIPERBARUI
═══════════════════════════════════════════════

1. resources/views/home.blade.php
   - Hero section sudah bagus, pertahankan
   - Bagian bawah: pastikan section pakai warna paper/surface konsisten
   - Tambahkan section fitur AI (Ara Guide) sebelum footer
   - CTA utama pakai <x-editorial.btn>

2. resources/views/destinations/index.blade.php
   - Masih pakai class lama: text-sunset-500, font-montserrat, bg-ocean-900
   - Ganti semua ke design token baru: text-clay, font-serif/sans, bg-ink
   - Search form: border rounded-full, input ringan, tombol clay
   - Filter pills: pakai border border-line, hover text-clay, active bg-clay text-white
   - Cards: pakai <x-editorial.card> atau buat card serupa

3. resources/views/destinations/show.blade.php
   - Hero sudah bagus (cinematic 92vh)
   - Content section bawah: pastikan pakai bg-paper, text-ink, border-line
   - Tab navigasi (Deskripsi / Fasilitas / Ulasan): style pill aktif = bg-ink text-paper
   - Tombol booking: bg-clay text-white, rounded-xl

4. resources/views/hotel/index.blade.php
   - Sama seperti destinations/index: ganti class lama ke design token baru

5. resources/views/hotel/show.blade.php
   - Hero sudah bagus
   - Room cards: bg-paper border border-line rounded-2xl, harga text-clay font-bold
   - Form booking inline: clean, label text-muted, input border-line

6. resources/views/paket_tour/index.blade.php
   - Ganti ke design token baru
   - Kartu tour: tampilkan durasi, harga, dan rating dengan tipografi jelas

7. resources/views/paket_tour/show.blade.php
   - Bersihkan class lama, gunakan design token
   - Gallery: grid 2-3 kolom, overflow-hidden rounded-2xl

8. resources/views/culture/index.blade.php
   - Ganti ke editorial style
   - Card budaya: image dominan, judul font-serif, lokasi text-muted

9. resources/views/user/dashboard.blade.php
   - Sudah cukup baik dengan bg-white border-slate
   - Selaraskan: pakai bg-paper, border-line, text-ink, text-muted, text-clay
   - Stats card: ikon dalam bg-surface, angka font-serif font-black text-ink

10. resources/views/livewire/travel-chat.blade.php
    - Chat bubble user: bg-clay text-white
    - Chat bubble AI: bg-surface border border-line text-ink
    - Input: border-line rounded-full, tombol kirim bg-clay

11. resources/views/booking/create.blade.php
    - Form clean: label text-muted text-xs uppercase tracking-widest
    - Input: bg-surface border-line rounded-xl focus:border-clay
    - Submit: bg-clay text-white font-bold rounded-xl

12. resources/views/transaction/success.blade.php
    - Halaman sukses: centered, ikon check besar text-clay
    - Minimal, clean, button back ke home pakai <x-editorial.btn>

═══════════════════════════════════════════════
ATURAN PENGGANTIAN CLASS
═══════════════════════════════════════════════

LAMA → BARU:
  bg-ocean-900, bg-slate-900       → bg-ink     (section gelap)
  bg-slate-800                     → bg-ink/90
  bg-slate-50, bg-gray-50          → bg-surface
  bg-white                         → bg-paper
  text-sunset-500, text-orange-500 → text-clay
  text-slate-800, text-gray-800    → text-ink
  text-slate-500, text-gray-500    → text-muted
  border-slate-200, border-gray-200 → border-line
  font-montserrat                  → font-serif  (heading besar)
  focus:border-sunset-500          → focus:border-clay focus:ring-1 focus:ring-clay/30

═══════════════════════════════════════════════
POLA KOMPONEN YANG HARUS KONSISTEN
═══════════════════════════════════════════════

PAGE HERO:
  <section class="relative min-h-[480px] bg-ink overflow-hidden">
    <img class="absolute inset-0 w-full h-full object-cover opacity-60">
    <div class="absolute inset-0 bg-gradient-to-b from-ink/30 to-ink/80"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-32 text-white">
      <h1 class="font-serif text-5xl md:text-6xl italic mb-4">...</h1>
    </div>
  </section>

SECTION HEADING:
  <div class="flex justify-between items-end mb-10">
    <div>
      <h2 class="font-serif text-3xl md:text-4xl text-ink mb-2">Judul</h2>
      <p class="text-muted text-sm">Subjudul</p>
    </div>
    <a href="#" class="text-sm text-muted hover:text-clay transition-colors">
      Lihat semua →
    </a>
  </div>

CARD STANDAR:
  <div class="bg-paper border border-line rounded-2xl overflow-hidden hover:shadow-md transition-all group">
    <div class="aspect-[4/3] overflow-hidden">
      <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
    </div>
    <div class="p-5">
      <span class="text-xs text-muted font-semibold uppercase tracking-wider">Kategori</span>
      <h3 class="font-serif text-lg text-ink mt-1 mb-2">Nama</h3>
      <p class="text-muted text-sm line-clamp-2">Deskripsi</p>
      <div class="mt-4 flex items-center justify-between">
        <span class="text-clay font-bold text-sm">Rp 0</span>
        <span class="text-xs text-muted flex items-center gap-1">
          <i class="fas fa-star text-amber-400 text-[10px]"></i> 4.8
        </span>
      </div>
    </div>
  </div>

TOMBOL UTAMA:
  <button class="inline-flex items-center gap-2 bg-clay text-white font-semibold
                 text-sm px-5 py-2.5 rounded-full hover:bg-clay/90
                 transition-all shadow-sm shadow-clay/20">
    Label
  </button>

TOMBOL SEKUNDER:
  <button class="inline-flex items-center gap-2 bg-paper text-ink font-semibold
                 text-sm px-5 py-2.5 rounded-full border border-line
                 hover:border-clay hover:text-clay transition-all">
    Label
  </button>

INPUT FORM:
  <input class="w-full bg-surface border border-line rounded-xl px-4 py-3
                text-sm text-ink placeholder:text-muted
                focus:outline-none focus:border-clay focus:ring-2 focus:ring-clay/10
                transition-all">

BADGE / TAG:
  <span class="inline-flex items-center gap-1.5 bg-clay/10 text-clay
               text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">
    Label
  </span>

═══════════════════════════════════════════════
CARA KERJA
═══════════════════════════════════════════════
1. Baca file asli sebelum mengedit (gunakan Read tool)
2. Update satu file, selesaikan, baru ke file berikutnya
3. Pertahankan semua logika PHP/Blade (@foreach, @if, @auth, dll)
4. Pertahankan semua nama route (route('...'))
5. Jangan ubah struktur data/variabel Blade ($destination, $hotel, dll)
6. Jangan hapus wire:model, wire:click, x-data, @click, Alpine directives
7. Setelah selesai semua, pastikan tidak ada class lama yang tersisa

Mulai dari: resources/views/destinations/index.blade.php
```
