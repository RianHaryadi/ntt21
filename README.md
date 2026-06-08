<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Version"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---

# 🌴 Wonderful NTT – Tourism Platform

Welcome to **Wonderful NTT**, a comprehensive tourism web platform dedicated to showcasing the beauty of **East Nusa Tenggara (NTT)**. Built with **Laravel 11**, **FilamentPHP**, and **Tailwind CSS**, this application provides a seamless experience for travelers to explore destinations, book accommodations, and discover curated tour packages.

---

## ✨ Key Features

- **🏝️ Destination Explorer**: Discover breathtaking destinations across the NTT region, filtered by category and location.
- **🏨 Smart Hotel Booking**: Browse hotels with detailed room types, real-time availability, and integrated booking management.
- **🎒 Curated Tour Packages**: Join pre-planned tour packages or customize your itinerary with group or per-item pricing.
- **🎟️ Ticketing System**: Automatic ticket generation for bookings with QR Code verification (ready for admin scanning).
- **💎 Promo & Discounts**: Integrated promo code system to offer dynamic discounts to users.
- **🔐 Admin Dashboard**: Powered by **FilamentPHP**, allowing admins to manage destinations, bookings, users, and transactions effortlessly.
- **📈 Financial Insights**: Exportable reports (PDF/Excel) for monitoring revenue and booking trends.

---

## 🧭 Tech Stack

| Layer | Technology |
| :--- | :--- |
| **Framework** | [Laravel 11](https://laravel.com/) |
| **Admin Panel** | [FilamentPHP v3](https://filamentphp.com/) |
| **Frontend** | [Blade](https://laravel.com/docs/11.x/blade), [Tailwind CSS](https://tailwindcss.com/) |
| **State Management** | [Livewire v3](https://livewire.laravel.com/) |
| **Build Tool** | [Vite](https://vitejs.dev/) |
| **Database** | MySQL / PostgreSQL |
| **Automation** | Python (Data Migration Scripts) |

---

## 🛠️ Installation & Setup

Follow these steps to get the project running locally:

### 1. Prerequisites
- PHP ≥ 8.2
- Composer
- Node.js & NPM
- MySQL

### 2. Clone & Install
```bash
# Clone the repository
git clone https://github.com/your-username/ntt21.git
cd ntt21

# Install dependencies
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```
*Don't forget to update your `.env` with your database credentials.*

### 4. Database Setup
```bash
# Run migrations and seed the database
php artisan migrate --seed
```

### 5. (Optional) Data Import via Python
If you want to import initial hotel/destination data from CSV:
```bash
cd python-import
pip install mysql-connector-python pandas
python import_csv_to_laravel.py
```

### 6. Run the Application
```bash
# Compile assets
npm run dev

# Start the server
php artisan serve
```

---

## 📁 Project Structure Overview

```plaintext
.
├── app/
│   ├── Filament/          # Filament Admin resources and pages
│   ├── Http/Controllers/   # Main web controllers
│   ├── Models/            # Eloquent models (Destination, Hotel, Booking, etc.)
│   └── Providers/         # Service providers
├── database/
│   ├── migrations/        # Database schema definitions
│   └── seeders/           # Initial data seeders
├── python-import/         # Python scripts for bulk data import
├── resources/
│   ├── views/             # Blade templates
│   └── css/               # Tailwind CSS entry points
├── routes/
│   ├── web.php            # Main web routes
│   └── api.php            # API endpoints (if any)
└── tailwind.config.js     # Tailwind CSS configuration
```

---

## 🤝 Contributing

Contributions are welcome! If you have suggestions for improvements or new features, feel free to open an issue or submit a pull request.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## ⚖️ License

Distributed under the MIT License. See `LICENSE` for more information.

---

<p align="center">
  Built with ❤️ for NTT Tourism.
</p>
