# Aplikasi Chat Sederhana

Aplikasi chat dua arah sederhana dengan antarmuka seperti WhatsApp, memungkinkan pengguna dan admin untuk berkomunikasi.

## Fitur

- Antarmuka pengguna yang modern dan minimalis
- Chat real-time antara pengguna dan admin
- Panel admin dengan sistem login
- Desain responsif
- Animasi pesan yang halus
- Mode Gelap / Dark Mode
- Bottom Navbar dengan pengaturan
- Penyimpanan preferensi tema
- Tombol sosial media
- Pencegahan pesan duplikat

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web server (Apache/Nginx)

## Instalasi

1. Clone repositori ini
2. Buat database baru dengan nama `chat_app`
3. Import file `database.sql` ke database Anda
4. Konfigurasi koneksi database di `config/database.php`
5. Pastikan web server Anda sudah berjalan
6. Akses aplikasi melalui browser

## Font

Aplikasi ini menggunakan font Amulya-Bold dan Synonim_Regular. Pastikan kedua font tersebut tersedia di folder `assets/fonts/`:
- Amulya-Bold.otf (untuk header)
- Synonym-Regular.otf (untuk teks umum)

## Struktur Proyek

```
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   └── logout.php
├── api/
│   ├── send_message.php
│   ├── get_messages.php
│   └── toggle_dark_mode.php
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── theme.css
│   ├── fonts/
│   │   ├── Amulya-Bold.otf
│   │   └── Synonym-Regular.otf 
│   └── js/
│       ├── script.js
│       ├── admin.js
│       └── theme.js
├── config/
│   └── database.php
├── includes/
│   └── functions.php
├── index.php
└── database.sql
```

## Penggunaan

1. Pengguna dapat mengakses halaman utama untuk mengirim pesan
2. Admin dapat login melalui `/admin/login.php`
   - Username: admin
   - Password: admin123
3. Admin dapat melihat dan membalas pesan di dashboard
4. Pengguna dan admin dapat mengaktifkan mode gelap melalui tombol pengaturan
5. Pengaturan tema disimpan dan akan tetap aktif setelah refresh atau login

## Perbaikan Bug

- Pesan duplikat: Diatasi dengan implementasi Set untuk melacak pesan
- Scrolling chat container: Diperbaiki dengan CSS dan JavaScript
- Border icon pada form login: Disesuaikan dengan tinggi yang tepat
- Tombol login admin: Diperbaiki agar bulat sempurna
- Dark mode pada admin panel: Diperbaiki agar dapat dimatikan setelah login

## Bottom Navbar

Bottom navbar menyediakan:
- Toggle Dark Mode dengan penyimpanan preferensi
- Pengaturan aplikasi
- Tautan ke sosial media

## Keamanan

- Pastikan untuk mengubah kredensial admin default
- Gunakan HTTPS untuk komunikasi yang aman
- Terapkan validasi input yang ketat
- Gunakan prepared statements untuk mencegah SQL injection

## Lisensi

Proyek ini dilisensikan di bawah MIT License - lihat file LICENSE untuk detail lebih lanjut. 