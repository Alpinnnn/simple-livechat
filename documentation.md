# Simple Live Chat - Dokumentasi Lengkap

## Deskripsi Aplikasi

Simple Live Chat adalah aplikasi chat dua arah dengan antarmuka modern mirip WhatsApp yang memungkinkan komunikasi antara pengguna dan admin. Aplikasi ini dibangun menggunakan PHP, MySQL, JavaScript, HTML, dan CSS dengan pendekatan desain responsif.

## Fitur Utama

- **Chat Real-time**: Komunikasi dua arah antara pengguna dan admin
- **Antarmuka Modern**: Desain minimalis mirip aplikasi chat populer
- **Panel Admin**: Sistem login admin untuk mengelola percakapan
- **Responsif**: Tampilan yang menyesuaikan dengan berbagai ukuran layar
- **Mode Gelap**: Pilihan tampilan terang atau gelap yang disimpan dalam session
- **Bottom Navbar**: Panel pengaturan dengan toggle mode gelap dan tautan sosial media
- **Animasi Pesan**: Efek transisi halus saat pesan masuk
- **Pencegahan Duplikasi**: Sistem untuk mencegah duplikasi pesan

## Persyaratan Sistem

- **Server**: PHP 7.4 atau lebih tinggi
- **Database**: MySQL 5.7 atau lebih tinggi
- **Web Server**: Apache/Nginx
- **Browser**: Chrome, Firefox, Safari, Edge versi terbaru

## Struktur Database

Database aplikasi menggunakan struktur sederhana dengan satu tabel utama:

```sql
CREATE DATABASE IF NOT EXISTS chat_app;
USE chat_app;

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    is_user BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Penjelasan Tabel

- `id`: Identifier unik untuk setiap pesan
- `message`: Isi pesan yang dikirim
- `is_user`: Boolean (1 = pesan dari pengguna, 0 = pesan dari admin)
- `created_at`: Timestamp kapan pesan dikirim

## Struktur Proyek

```
├── admin/                     # Direktori untuk panel admin
│   ├── login.php              # Halaman login admin
│   ├── dashboard.php          # Dashboard admin untuk melihat & membalas pesan
│   └── logout.php             # Script untuk logout admin
├── api/                       # Endpoint API untuk komunikasi
│   ├── send_message.php       # API untuk mengirim pesan
│   ├── get_messages.php       # API untuk mengambil pesan
│   └── toggle_dark_mode.php   # API untuk mengaktifkan/menonaktifkan mode gelap
├── assets/                    # Aset statis aplikasi
│   ├── css/                   # File CSS
│   │   ├── style.css          # Gaya utama
│   │   └── theme.css          # Gaya untuk tema (light/dark mode)
│   ├── fonts/                 # Font kustom
│   │   ├── Amulya-Bold.otf    # Font untuk header
│   │   └── Synonym-Regular.otf # Font untuk teks umum
│   └── js/                    # File JavaScript
│       ├── script.js          # Script untuk halaman utama
│       ├── admin.js           # Script untuk panel admin
│       └── theme.js           # Script untuk pengaturan tema
├── config/                    # Konfigurasi aplikasi
│   └── database.php           # Konfigurasi koneksi database
├── includes/                  # File include
│   └── functions.php          # Fungsi-fungsi umum
├── index.php                  # Halaman utama aplikasi
└── database.sql               # SQL untuk setup database
```

## Teknologi yang Digunakan

- **Frontend**:
  - HTML5
  - CSS3 (dengan fleksibilitas Bootstrap 5)
  - JavaScript (vanilla JS)
  - Font Awesome (untuk ikon)

- **Backend**:
  - PHP (vanilla PHP tanpa framework)
  - MySQL (dengan PDO untuk koneksi database)

- **Teknik & Metode**:
  - AJAX (untuk komunikasi asynchronous)
  - Interval polling (untuk pembaruan pesan)
  - Session storage (untuk preferensi tema)
  - Prepared statements (untuk keamanan)

## Alur Kerja Aplikasi

### Alur Pengguna

1. Pengguna mengakses `index.php`
2. Sistem mengecek apakah mode gelap aktif melalui fungsi `isDarkMode()`
3. Pengguna mengirim pesan melalui form
4. JavaScript menangkap event submit form
5. Pesan dikirim ke `api/send_message.php` menggunakan fetch API
6. Data disimpan ke database dan ditampilkan di chat container
7. Script melakukan polling setiap 3 detik ke `api/get_messages.php` untuk mendapatkan pesan baru
8. Pesan baru ditampilkan dengan animasi dan scroll otomatis ke bawah

### Alur Admin

1. Admin mengakses `admin/login.php`
2. Memasukkan kredensial (username: admin, password: admin123)
3. Setelah login berhasil, admin diarahkan ke `admin/dashboard.php`
4. Dashboard menampilkan semua percakapan dengan pengguna
5. Admin dapat membalas pesan, yang kemudian muncul di sisi pengguna
6. Admin dapat logout melalui tombol logout yang memanggil `admin/logout.php`

## Fungsi-fungsi Utama

### Penanganan Pesan

```javascript
// Mengirim pesan
fetch('api/send_message.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ message: message })
})

// Mengambil pesan (polling)
setInterval(fetchNewMessages, 3000);
```

### Pencegahan Duplikasi Pesan

```javascript
// Menggunakan Set JavaScript untuk melacak pesan
const displayedMessages = new Set();

// Cek pesan sebelum ditampilkan
if (displayedMessages.has(messageId)) return;
```

### Toggle Mode Gelap

```javascript
// PHP: Cek status mode gelap
function isDarkMode() {
    if (isset($_SESSION['dark_mode'])) {
        return $_SESSION['dark_mode'] === true || $_SESSION['dark_mode'] === 1 || $_SESSION['dark_mode'] === '1';
    }
    return false;
}

// JavaScript: Toggle mode gelap
darkModeToggle.addEventListener('change', function() {
    fetch('api/toggle_dark_mode.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            darkMode: this.checked 
        })
    });
});
```

## Panduan Instalasi

1. **Persiapan Server**:
   - Pastikan PHP 7.4+ dan MySQL 5.7+ sudah terinstall
   - Konfigurasikan web server Apache/Nginx

2. **Persiapan Database**:
   ```bash
   mysql -u username -p < database.sql
   ```
   Atau import `database.sql` melalui phpMyAdmin

3. **Konfigurasi Koneksi Database**:
   Edit file `config/database.php` dan sesuaikan parameter berikut:
   ```php
   private $host = "localhost";     // Host database
   private $db_name = "chat_app";   // Nama database
   private $username = "root";      // Username database
   private $password = "";          // Password database
   ```

4. **Font**:
   - Pastikan font Amulya-Bold.otf dan Synonym-Regular.otf tersedia di folder `assets/fonts/`
   - Jika tidak tersedia, aplikasi akan menggunakan font fallback

5. **Pengujian**:
   - Akses aplikasi melalui browser dengan mengunjungi alamat server
   - Login admin dengan kredensial default (username: admin, password: admin123)

## Keamanan

### Praktik Keamanan yang Diimplementasikan

1. **Prepared Statements**:
   - Semua query database menggunakan prepared statements untuk mencegah SQL injection
   ```php
   $stmt = $db->prepare($query);
   $stmt->bindParam(":message", $data->message);
   ```

2. **Session Management**:
   - Session dibuat hanya saat diperlukan
   - Validasi status session

3. **Input Validation**:
   - Validasi input di sisi server dan klien
   - Pesan kosong tidak akan dikirim

### Rekomendasi Keamanan Tambahan

1. **Ubah Kredensial Default**:
   - Ganti username dan password admin default
   - Gunakan password yang kuat

2. **Gunakan HTTPS**:
   - Implementasikan SSL/TLS untuk enkripsi komunikasi

3. **Rate Limiting**:
   - Batasi jumlah API request per waktu tertentu

4. **Sanitasi Output**:
   - Tambahkan sanitasi output untuk mencegah XSS

## Pengembangan Lebih Lanjut

### Potensi Pengembangan

1. **Sistem Notifikasi**:
   - Notifikasi browser ketika ada pesan baru
   - Notifikasi email untuk admin

2. **Fitur Tambahan**:
   - Upload gambar dan file
   - Status pesan (terkirim, dibaca)
   - Multiple admin dengan level akses berbeda

3. **Optimasi Performa**:
   - Implementasi WebSockets untuk real-time chat
   - Pagination untuk riwayat chat yang panjang
   - Caching untuk mengurangi beban database

4. **UX Enhancements**:
   - Typing indicator
   - Read receipts
   - Message reactions

## Troubleshooting

### Masalah Umum dan Solusi

1. **Pesan Tidak Muncul**:
   - Pastikan koneksi database benar
   - Periksa error log PHP
   - Verifikasi bahwa JavaScript berjalan di browser

2. **Mode Gelap Tidak Berfungsi**:
   - Pastikan session PHP berfungsi dengan benar
   - Periksa cookie browser

3. **Masalah Responsif**:
   - Pastikan viewport meta tag ada di header
   - Verifikasi CSS responsif berjalan dengan benar

### Debugging

- File `api/debug_session.php` tersedia untuk debugging session
- Gunakan browser developer tools untuk debugging JavaScript

## Lisensi

Aplikasi ini dilisensikan di bawah MIT License. Lihat file LICENSE untuk detail lebih lanjut.

---

Dibuat oleh: Alpin (https://alpinnnn.vercel.app)  
GitHub: https://github.com/Alpinnnn 