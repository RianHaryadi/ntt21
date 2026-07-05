# Product Requirements Document (PRD)
## Project: Pesona NTT — Tourism Platform v2.0 (Next.js)

Pesona NTT adalah platform pariwisata digital komprehensif yang dirancang untuk memperkenalkan, mempromosikan, dan mempermudah pemesanan layanan wisata di Provinsi Nusa Tenggara Timur (NTT). Versi 2.0 dibangun ulang menggunakan **Next.js 15** dengan pendekatan modern full-stack untuk performa, SEO, dan skalabilitas yang lebih baik.

---

## 1. Tujuan Platform (Product Goal)

| Tujuan | Deskripsi |
|--------|-----------|
| Eksplorasi NTT | Direktori destinasi wisata, hotel, paket tour, dan konten budaya NTT secara interaktif |
| Sistem Pemesanan Terintegrasi | Booking hotel, paket tour, dan destinasi dengan pembayaran otomatis via payment gateway |
| AI Travel Assistant | Rekomendasi perjalanan personal berbasis AI (Anthropic Claude) |
| E-Tiket & QR Code | Tiket elektronik otomatis dengan QR Code untuk verifikasi lapangan |
| Sosial & Review | Ulasan dan rating dari pengguna yang sudah melakukan perjalanan |
| Admin Panel | Manajemen konten, booking, transaksi, dan laporan finansial |

---

## 2. Tech Stack

### Frontend & Backend
| Layer | Teknologi |
|-------|-----------|
| Framework | Next.js 15 (App Router) |
| Bahasa | TypeScript |
| Styling | Tailwind CSS v4 |
| UI Components | shadcn/ui |
| State Management | Zustand |
| Form | React Hook Form + Zod |
| Peta | Leaflet.js |

### Backend & Database
| Layer | Teknologi |
|-------|-----------|
| API | Next.js Route Handlers (REST) |
| ORM | Prisma |
| Database | PostgreSQL |
| Cache | Redis (Upstash) |
| Auth | NextAuth.js v5 |
| Storage | Cloudinary (gambar) |

### Integrasi Eksternal
| Layanan | Kegunaan |
|---------|----------|
| Midtrans | Payment gateway (VA, QRIS, kartu kredit) |
| Anthropic Claude API | AI travel recommendation |
| Resend | Pengiriman email transaksional |
| Google OAuth | Social login |

### Deployment
| Infrastruktur | Teknologi |
|--------------|-----------|
| Hosting | Vercel |
| Database | Supabase PostgreSQL |
| Cache | Upstash Redis |

---

## 3. Modul & Fitur Lengkap

### MODUL 1 — Autentikasi & Profil User

#### F-01 Register & Login
- Register dengan email dan password
- Login dengan email dan password
- Login dengan akun Google (OAuth)
- Validasi form real-time (Zod)
- Session management via NextAuth.js

#### F-02 Profil User
- Halaman profil dengan data diri
- Edit nama, nomor telepon, foto profil
- Ganti password
- Riwayat booking (hotel, paket tour, destinasi)
- Tiket aktif dan tiket lama
- Daftar wishlist tersimpan

---

### MODUL 2 — Eksplorasi Konten

#### F-03 Halaman Home
- Hero section dengan foto destinasi unggulan NTT
- Section destinasi terpopuler (berdasarkan rating)
- Section hotel rekomendasi
- Section paket tour unggulan
- Section konten budaya terbaru
- Statistik platform (jumlah destinasi, hotel, wisatawan)

#### F-04 Destinasi Wisata
- Listing destinasi dengan infinite scroll atau pagination
- Filter: kategori (pantai, gunung, budaya, dll), lokasi kabupaten, harga, rating
- Pencarian teks real-time
- Kartu destinasi: foto, nama, lokasi, harga, rating bintang, jumlah ulasan
- Halaman detail destinasi:
  - Galeri foto (lightbox)
  - Deskripsi lengkap
  - Lokasi di peta interaktif (Leaflet.js)
  - Tombol buka Google Maps
  - Rating rata-rata & ulasan pengguna
  - Tombol booking langsung
  - Tombol tambah ke wishlist

#### F-05 Hotel
- Listing hotel dengan filter: lokasi, harga per malam, tipe kamar
- Kartu hotel: foto, nama, lokasi, harga mulai dari, rating
- Halaman detail hotel:
  - Galeri foto
  - Deskripsi & fasilitas
  - Tabel tipe kamar (single, double, family) dengan harga dan stok
  - Kalender ketersediaan kamar visual (tanggal penuh ditandai)
  - Rating rata-rata & ulasan pengguna
  - Tombol booking

#### F-06 Paket Tour
- Listing paket tour dengan filter: durasi (hari), harga, lokasi
- Kartu paket: foto, nama, durasi, harga, lokasi
- Halaman detail paket:
  - Deskripsi & itinerary singkat
  - Termasuk hotel atau tidak
  - Foto-foto
  - Tombol booking

#### F-07 Konten Budaya NTT
- Listing artikel budaya dengan filter kategori (tarian, pakaian, kuliner, adat)
- Halaman detail artikel budaya dengan konten lengkap dan foto

#### F-08 Peta Interaktif
- Halaman khusus peta NTT
- Semua destinasi ditampilkan sebagai marker
- Klik marker → popup info singkat + link ke detail
- Filter marker berdasarkan kategori

---

### MODUL 3 — Booking & Transaksi

#### F-09 Booking Hotel
- Form booking: pilih tipe kamar, tanggal check-in & check-out, jumlah tamu
- Kalender interaktif yang memblokir tanggal tidak tersedia
- Kalkulasi otomatis real-time:
  - Base cost = harga kamar × jumlah malam
  - Pajak = base cost × 10%
  - Service charge = base cost × 5%
  - Diskon jika kode promo valid
  - Total = base + pajak + service - diskon
- Input kode promo dengan validasi real-time ke server
- Ringkasan pesanan sebelum konfirmasi
- Buat booking → status awal: `pending`
- Email konfirmasi booking terkirim otomatis

#### F-10 Booking Paket Tour
- Form booking: pilih paket, tanggal, jumlah tiket
- Kalkulasi: subtotal × jumlah tiket - diskon
- Input kode promo
- Ringkasan pesanan
- Buat booking & transaksi → status: `pending`

#### F-11 Booking Destinasi Langsung
- Form booking: pilih tanggal kunjungan, jumlah tiket
- Kalkulasi: harga tiket × jumlah
- Buat transaksi langsung

#### F-12 Pembayaran (Midtrans)
- Halaman pembayaran dengan pilihan metode:
  - Transfer bank (Virtual Account BCA, BNI, BRI, Mandiri)
  - QRIS
  - Kartu kredit/debit
- Redirect ke Midtrans Snap UI
- Webhook handler untuk update status otomatis
- Status transaksi update real-time setelah pembayaran berhasil
- Redirect ke halaman sukses setelah bayar

#### F-13 Pembatalan & Refund
- User bisa request cancel dari dashboard
- Alur status: `confirmed → cancellation_requested → refunded`
- Admin approve/reject request di panel admin
- Email notifikasi perubahan status ke user

#### F-14 Cek Status Booking
- Halaman publik cek booking tanpa login
- Input nomor booking → tampil status terkini

---

### MODUL 4 — E-Tiket

#### F-15 Penerbitan Tiket Otomatis
- Tiket dibuat otomatis saat transaksi berstatus `paid`
- Satu tiket per kuantitas yang dibeli
- Format kode: `TIX-[booking_code]-[4 karakter random]`
- QR Code unik per tiket (berisi kode tiket)

#### F-16 Pengiriman & Akses Tiket
- Email berisi semua tiket dikirim ke user setelah bayar (via Resend)
- Tiket dapat diakses di dashboard user kapan saja
- Halaman tiket individual dengan tampilan besar QR Code (siap scan)
- Status tiket: `active`, `used`, `expired`

---

### MODUL 5 — Review & Rating

#### F-17 Ulasan Pengguna
- Hanya user yang sudah menyelesaikan booking bisa beri ulasan
- Rating bintang 1-5
- Komentar teks
- Foto opsional (upload ke Cloudinary)
- Satu ulasan per booking per item (destinasi/hotel)

#### F-18 Tampilan Ulasan
- Rating rata-rata ditampilkan di kartu & halaman detail
- Distribusi bintang (berapa persen 5 bintang, 4 bintang, dll)
- Daftar ulasan terbaru dengan nama user & tanggal
- Pagination ulasan

---

### MODUL 6 — Wishlist

#### F-19 Simpan Favorit
- Tombol wishlist (ikon hati) di setiap kartu destinasi & hotel
- Toggle: tambah/hapus dari wishlist
- Login required untuk wishlist
- Halaman "Favorit Saya" di dashboard user

---

### MODUL 7 — Notifikasi

#### F-20 Notifikasi In-App
- Bell icon di navbar dengan badge jumlah notif belum dibaca
- Jenis notifikasi:
  - Booking berhasil dibuat
  - Pembayaran dikonfirmasi
  - Tiket diterbitkan
  - Status booking berubah (approve, cancel, refund)
- Klik notifikasi → redirect ke halaman terkait
- Tandai semua sebagai sudah dibaca

---

### MODUL 8 — AI Travel Assistant

#### F-21 Travel Chat AI
- Halaman chat interaktif dengan AI (Anthropic Claude)
- AI menanyakan preferensi satu per satu:
  1. Wilayah NTT yang diminati (Flores, Sumba, Timor, dll)
  2. Budget perjalanan
  3. Jumlah orang & durasi
  4. Preferensi pengalaman (alam, budaya, petualangan, relaksasi)
  5. Destinasi yang ingin dihindari
  6. Preferensi akomodasi
  7. Pantangan makanan
- Streaming response (teks muncul real-time, bukan tunggu selesai)
- Riwayat chat tersimpan per sesi di database

#### F-22 Hasil Rekomendasi AI
- Halaman hasil dengan layout terstruktur:
  - Destinasi tersembunyi (hidden gems) dengan deskripsi
  - Itinerary hari-per-hari dengan estimasi waktu
  - Rekomendasi akomodasi (2+ pilihan per lokasi)
  - Rekomendasi kuliner lokal
  - Rincian estimasi budget
  - Tips praktis (waktu terbaik, adat setempat, packing list)
- Tombol langsung booking hotel/paket tour yang direkomendasikan
- Simpan & akses ulang hasil rekomendasi dari dashboard

---

### MODUL 9 — Admin Panel

#### F-23 Dashboard Admin
- Total revenue, pertumbuhan omset, jumlah transaksi sukses
- Grafik transaksi bulanan (line chart)
- Paket tour terpopuler (bar chart)
- Distribusi metode pembayaran (pie chart)
- Tingkat keterisian kamar hotel (tabel)
- Jumlah ulasan baru & rating rata-rata platform

#### F-24 Manajemen Konten
- CRUD Destinasi (dengan upload foto multi-gambar ke Cloudinary)
- CRUD Hotel & tipe kamar
- CRUD Paket Tour
- CRUD Konten Budaya
- CRUD Kode Promo (dengan pengaturan tanggal & tipe diskon)

#### F-25 Manajemen Booking & Transaksi
- Tabel semua booking hotel dengan filter status
- Approve / cancel / mark checked-out booking hotel
- Tabel semua transaksi dengan filter status
- Konfirmasi manual pembayaran (backup jika webhook gagal)
- Approve / reject request pembatalan

#### F-26 Manajemen User & Review
- Daftar semua user terdaftar
- Hapus atau nonaktifkan akun user
- Moderasi ulasan (hapus ulasan tidak pantas)

---

## 4. Database Schema

```prisma
model User {
  id            String    @id @default(cuid())
  name          String
  email         String    @unique
  password      String?
  phone         String?
  avatar        String?
  provider      String?   // "google" | null
  providerId    String?
  role          Role      @default(USER)
  createdAt     DateTime  @default(now())

  bookingHotels   BookingHotel[]
  tourBookings    TourBooking[]
  transactions    Transaction[]
  reviews         Review[]
  wishlists       Wishlist[]
  notifications   Notification[]
  chatSessions    TravelChatSession[]
}

enum Role { USER ADMIN }

model Destination {
  id          String   @id @default(cuid())
  name        String
  location    String
  category    String
  description String
  price       Decimal
  rating      Float    @default(0)
  latitude    Float?
  longitude   Float?
  mapsUrl     String?
  photos      String[]
  status      String   @default("active")
  createdAt   DateTime @default(now())

  hotels        Hotel[]
  tourPackages  TourPackage[]
  transactions  Transaction[]
  reviews       Review[]
  wishlists     Wishlist[]
}

model Hotel {
  id              String   @id @default(cuid())
  destinationId   String
  name            String
  address         String
  location        String
  description     String?
  photos          String[]
  priceSingle     Decimal
  priceDouble     Decimal
  priceFamily     Decimal
  roomCountSingle Int      @default(0)
  roomCountDouble Int      @default(0)
  roomCountFamily Int      @default(0)
  rating          Float    @default(0)
  createdAt       DateTime @default(now())

  destination   Destination    @relation(fields: [destinationId], references: [id])
  rooms         HotelRoom[]
  bookings      BookingHotel[]
  tourBookings  TourBooking[]
  reviews       Review[]
  wishlists     Wishlist[]
}

model HotelRoom {
  id           String   @id @default(cuid())
  hotelId      String
  roomNumber   String
  type         String   // single | double | family
  status       String   @default("available")

  hotel        Hotel         @relation(fields: [hotelId], references: [id])
  bookings     BookingHotel[]
}

model BookingHotel {
  id              String   @id @default(cuid())
  bookingNumber   String   @unique
  userId          String
  hotelId         String
  hotelRoomId     String?
  roomType        String
  checkInDate     DateTime
  checkOutDate    DateTime
  nightCount      Int
  guestCount      Int
  basePrice       Decimal
  tax             Decimal
  serviceCharge   Decimal
  discountAmount  Decimal  @default(0)
  totalPrice      Decimal
  status          String   @default("pending")
  promoCodeId     String?
  createdAt       DateTime @default(now())

  user        User          @relation(fields: [userId], references: [id])
  hotel       Hotel         @relation(fields: [hotelId], references: [id])
  room        HotelRoom?    @relation(fields: [hotelRoomId], references: [id])
  promoCode   CodePromotion? @relation(fields: [promoCodeId], references: [id])
  transaction Transaction?
  review      Review?
}

model TourPackage {
  id             String   @id @default(cuid())
  destinationId  String
  name           String
  description    String
  duration       Int
  price          Decimal
  includesHotel  Boolean  @default(false)
  photos         String[]
  createdAt      DateTime @default(now())

  destination  Destination   @relation(fields: [destinationId], references: [id])
  bookings     TourBooking[]
  transactions Transaction[]
}

model TourBooking {
  id             String   @id @default(cuid())
  bookingNumber  String   @unique
  userId         String
  tourPackageId  String
  hotelId        String?
  customerName   String
  customerEmail  String
  customerPhone  String
  visitDate      DateTime
  numberOfPax    Int
  subtotal       Decimal
  discount       Decimal  @default(0)
  total          Decimal
  status         String   @default("pending")
  createdAt      DateTime @default(now())

  user         User         @relation(fields: [userId], references: [id])
  tourPackage  TourPackage  @relation(fields: [tourPackageId], references: [id])
  hotel        Hotel?       @relation(fields: [hotelId], references: [id])
  transaction  Transaction?
}

model Transaction {
  id              String   @id @default(cuid())
  bookingCode     String   @unique
  userId          String
  tourPackageId   String?
  destinationId   String?
  bookingHotelId  String?  @unique
  tourBookingId   String?  @unique
  amount          Decimal
  paymentMethod   String?
  midtransOrderId String?  @unique
  midtransToken   String?
  status          String   @default("pending")
  paidAt          DateTime?
  createdAt       DateTime @default(now())

  user         User         @relation(fields: [userId], references: [id])
  tourPackage  TourPackage? @relation(fields: [tourPackageId], references: [id])
  destination  Destination? @relation(fields: [destinationId], references: [id])
  bookingHotel BookingHotel? @relation(fields: [bookingHotelId], references: [id])
  tourBooking  TourBooking?  @relation(fields: [tourBookingId], references: [id])
  tickets      Ticket[]
  promoCode    CodePromotion? @relation(fields: [promoCodeId], references: [id])
  promoCodeId  String?
}

model Ticket {
  id            String   @id @default(cuid())
  transactionId String
  ticketCode    String   @unique
  qrCode        String
  status        String   @default("active")
  usedAt        DateTime?
  expiresAt     DateTime?
  createdAt     DateTime @default(now())

  transaction Transaction @relation(fields: [transactionId], references: [id])
}

model CodePromotion {
  id               String    @id @default(cuid())
  code             String    @unique
  description      String?
  discountPercent  Float?
  discountAmount   Decimal?
  minPurchase      Decimal?
  maxDiscount      Decimal?
  validFrom        DateTime?
  validUntil       DateTime?
  usageLimit       Int?
  usageCount       Int       @default(0)
  active           Boolean   @default(true)
  createdAt        DateTime  @default(now())

  bookingHotels BookingHotel[]
  transactions  Transaction[]
}

model Review {
  id              String   @id @default(cuid())
  userId          String
  reviewableType  String   // "destination" | "hotel"
  destinationId   String?
  hotelId         String?
  bookingHotelId  String?  @unique
  rating          Int
  body            String
  photos          String[]
  createdAt       DateTime @default(now())

  user        User         @relation(fields: [userId], references: [id])
  destination Destination? @relation(fields: [destinationId], references: [id])
  hotel       Hotel?       @relation(fields: [hotelId], references: [id])
  booking     BookingHotel? @relation(fields: [bookingHotelId], references: [id])
}

model Wishlist {
  id             String   @id @default(cuid())
  userId         String
  wishlistType   String   // "destination" | "hotel"
  destinationId  String?
  hotelId        String?
  createdAt      DateTime @default(now())

  user        User         @relation(fields: [userId], references: [id])
  destination Destination? @relation(fields: [destinationId], references: [id])
  hotel       Hotel?       @relation(fields: [hotelId], references: [id])

  @@unique([userId, destinationId])
  @@unique([userId, hotelId])
}

model Notification {
  id        String   @id @default(cuid())
  userId    String
  title     String
  body      String
  type      String
  link      String?
  readAt    DateTime?
  createdAt DateTime @default(now())

  user User @relation(fields: [userId], references: [id])
}

model Culture {
  id          String   @id @default(cuid())
  title       String
  category    String
  description String
  photos      String[]
  tags        String[]
  createdAt   DateTime @default(now())
}

model TravelChatSession {
  id                String   @id @default(cuid())
  userId            String?
  sessionToken      String   @unique
  status            String   @default("active")
  recommendationRaw String?
  createdAt         DateTime @default(now())

  user     User?         @relation(fields: [userId], references: [id])
  messages ChatMessage[]
}

model ChatMessage {
  id        String   @id @default(cuid())
  sessionId String
  role      String   // "user" | "assistant"
  content   String
  createdAt DateTime @default(now())

  session TravelChatSession @relation(fields: [sessionId], references: [id])
}
```

---

## 5. API Routes

### Auth
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/auth/register` | Register user baru |
| POST | `/api/auth/[...nextauth]` | NextAuth handler (login, OAuth) |

### Destinasi
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/destinations` | List destinasi (filter, search, paginate) |
| GET | `/api/destinations/[id]` | Detail destinasi |
| POST | `/api/destinations` | Buat destinasi (admin) |
| PUT | `/api/destinations/[id]` | Update destinasi (admin) |
| DELETE | `/api/destinations/[id]` | Hapus destinasi (admin) |

### Hotel
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/hotels` | List hotel |
| GET | `/api/hotels/[id]` | Detail hotel |
| GET | `/api/hotels/[id]/availability` | Cek ketersediaan kamar per tanggal |
| POST | `/api/hotels` | Buat hotel (admin) |

### Booking
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/bookings/hotel` | Buat booking hotel |
| POST | `/api/bookings/tour` | Buat booking paket tour |
| POST | `/api/bookings/destination` | Buat booking destinasi |
| GET | `/api/bookings/check/[bookingNumber]` | Cek status booking publik |
| POST | `/api/bookings/[id]/cancel` | Request pembatalan |

### Transaksi & Pembayaran
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/transactions/[bookingCode]/pay` | Buat Midtrans token |
| POST | `/api/webhooks/midtrans` | Webhook Midtrans (update status) |

### Promo
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/promos/validate` | Validasi kode promo |

### Review
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/reviews?type=destination&id=x` | List ulasan |
| POST | `/api/reviews` | Buat ulasan (user terautentikasi) |
| DELETE | `/api/reviews/[id]` | Hapus ulasan (admin/pemilik) |

### Wishlist
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/wishlist` | List wishlist user |
| POST | `/api/wishlist` | Toggle wishlist |

### Notifikasi
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/notifications` | List notifikasi user |
| PUT | `/api/notifications/read-all` | Tandai semua dibaca |

### AI Chat
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/travel/chat` | Kirim pesan & streaming response |
| GET | `/api/travel/sessions` | List sesi chat user |
| GET | `/api/travel/recommendation/[token]` | Ambil hasil rekomendasi |

### Admin
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/admin/dashboard` | Statistik dashboard |
| GET | `/api/admin/bookings` | Semua booking |
| PUT | `/api/admin/bookings/[id]/status` | Update status booking |
| GET | `/api/admin/transactions` | Semua transaksi |
| GET | `/api/admin/users` | Semua user |

---

## 6. Alur Status & Logika Bisnis

### Status Booking Hotel
```
pending → approve → checked-out
        ↓
      failed / cancelled
```

### Status Transaksi
```
pending → paid → confirmed → completed
        ↓
      cancelled / expired
```

### Kalkulasi Harga Hotel
```
Base Cost      = Harga Kamar × Jumlah Malam
Pajak          = Base Cost × 10%
Service Charge = Base Cost × 5%
Diskon         = Nominal atau Persen dari Base Cost
Total          = Base Cost + Pajak + Service Charge − Diskon
```

### Aturan Validasi Promo
- Status harus `active = true`
- Tanggal sekarang berada antara `validFrom` dan `validUntil`
- Jumlah pemakaian belum melebihi `usageLimit` (jika ada)
- Total belanja melebihi `minPurchase` (jika ada)
- Validasi dilakukan di sisi server, bukan client

### Manajemen Inventaris Kamar
- Kamar berkurang otomatis saat booking berstatus `approve`
- Kamar dikembalikan otomatis jika booking `cancelled` atau `failed`
- Jika tipe kamar diganti pada booking `approve`: stok tipe lama +1, tipe baru -1

### Penerbitan E-Tiket
- Tiket dibuat otomatis saat transaksi `paid` via webhook Midtrans
- Satu tiket per kuantitas yang dipesan
- Email dengan semua tiket dikirim via Resend
- QR Code di-generate dari kode tiket unik

---

## 7. Halaman (Page Structure)

### Public Pages
```
/                           → Home
/destinations               → Listing destinasi
/destinations/[id]          → Detail destinasi
/hotels                     → Listing hotel
/hotels/[id]                → Detail hotel
/paket-tour                 → Listing paket tour
/paket-tour/[id]            → Detail paket tour
/cultures                   → Listing budaya
/cultures/[id]              → Detail budaya
/map                        → Peta interaktif
/booking/check              → Cek status booking publik
/auth/login                 → Halaman login
/auth/register              → Halaman register
```

### Protected Pages (login required)
```
/dashboard                  → Dashboard user
/dashboard/bookings         → Riwayat booking
/dashboard/tickets          → Tiket aktif & lama
/dashboard/wishlist         → Favorit tersimpan
/dashboard/profile          → Edit profil
/booking/hotel/[id]         → Form booking hotel
/booking/tour/[id]          → Form booking paket tour
/booking/destination/[id]   → Form booking destinasi
/transactions/[code]/pay    → Halaman pembayaran
/transactions/[code]/success→ Halaman sukses pembayaran
/travel/chat                → AI travel chat
/travel/recommendation/[token] → Hasil rekomendasi AI
```

### Admin Pages
```
/admin                      → Dashboard admin
/admin/destinations         → Kelola destinasi
/admin/hotels               → Kelola hotel
/admin/tour-packages        → Kelola paket tour
/admin/cultures             → Kelola konten budaya
/admin/bookings             → Kelola booking
/admin/transactions         → Kelola transaksi
/admin/promos               → Kelola kode promo
/admin/users                → Kelola user
/admin/reviews              → Moderasi ulasan
```

---

## 8. Rencana Pengembangan (Development Phases)

### Phase 1 — Foundation (Minggu 1-2)
- [ ] Setup project Next.js 15 + TypeScript + Tailwind
- [ ] Setup Prisma + PostgreSQL (Supabase)
- [ ] Implementasi NextAuth.js (email + Google OAuth)
- [ ] Layout dasar (navbar, footer)
- [ ] Halaman home, listing destinasi, listing hotel, listing paket tour
- [ ] Halaman detail destinasi, hotel, paket tour
- [ ] Peta interaktif dengan Leaflet.js

### Phase 2 — Booking & Payment (Minggu 3-4)
- [ ] Form booking hotel dengan kalender ketersediaan
- [ ] Form booking paket tour & destinasi
- [ ] Validasi kode promo (server-side)
- [ ] Integrasi Midtrans (Snap)
- [ ] Webhook Midtrans untuk update status otomatis
- [ ] Penerbitan e-tiket otomatis + QR Code
- [ ] Email tiket via Resend

### Phase 3 — User Features (Minggu 5)
- [ ] Dashboard user (riwayat, tiket, profil)
- [ ] Sistem review & rating
- [ ] Wishlist
- [ ] Notifikasi in-app
- [ ] Pembatalan booking & request refund

### Phase 4 — AI & Admin (Minggu 6)
- [ ] Integrasi AI chat (Anthropic Claude) dengan streaming
- [ ] Halaman hasil rekomendasi AI
- [ ] Admin panel lengkap (CRUD semua konten)
- [ ] Dashboard statistik admin

### Phase 5 — Polish & Launch (Minggu 7)
- [ ] Optimasi performa (caching Redis, image optimization)
- [ ] SEO (metadata, sitemap, OpenGraph)
- [ ] Konten budaya NTT
- [ ] Testing end-to-end
- [ ] Deploy ke Vercel

---

## 9. Rencana Pengujian

### Pengujian Fungsional
- Alur register, login, OAuth Google
- Form booking hotel: kalkulasi harga dengan dan tanpa promo
- Proses pembayaran Midtrans end-to-end (gunakan sandbox)
- Penerbitan tiket otomatis setelah webhook Midtrans diterima
- Review hanya bisa dikirim oleh user yang sudah selesai booking
- Toggle wishlist tambah dan hapus

### Pengujian Performa
- Lighthouse score target: Performance > 90, SEO > 95
- Response time API target: < 200ms untuk halaman listing
- Caching: verifikasi Redis menyimpan data yang tepat

### Pengujian Keamanan
- Validasi promo di sisi server tidak bisa di-bypass dari client
- Route admin tidak bisa diakses user biasa
- Route protected tidak bisa diakses tanpa login
- Webhook Midtrans memverifikasi signature sebelum update status
