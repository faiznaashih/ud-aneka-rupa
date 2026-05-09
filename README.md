# 🍪 UD Aneka Rupa — Website Sistem Informasi & Pemesanan Kerupuk

Website fullstack PHP Native + MySQL untuk skripsi.

---

## 📁 Struktur Folder

```
/project
├── /assets
│   ├── /css
│   │   ├── style.css          ← CSS utama (frontend)
│   │   └── admin.css          ← CSS tambahan admin
│   ├── /js
│   │   └── main.js            ← JavaScript utama
│   └── /images
│       └── /produk/           ← Upload gambar produk (auto-created)
├── /config
│   └── config.php             ← Koneksi DB + helper functions
├── /admin
│   ├── /includes
│   │   ├── header_admin.php   ← Header admin panel
│   │   └── footer_admin.php   ← Footer admin panel
│   ├── index.php              ← Redirect ke dashboard
│   ├── login.php              ← Halaman login admin
│   ├── logout.php             ← Proses logout
│   ├── dashboard.php          ← Dashboard statistik
│   ├── produk.php             ← CRUD produk
│   ├── pesanan.php            ← Kelola pesanan
│   └── pelanggan.php          ← Data pelanggan
├── /includes
│   ├── header.php             ← Header frontend (navbar)
│   └── footer.php             ← Footer frontend
├── database.sql               ← Script SQL database
├── index.php                  ← Beranda / Landing page
├── produk.php                 ← Daftar produk + search + filter
├── detail.php                 ← Detail produk
├── pesan.php                  ← Form pemesanan
└── cek_status.php             ← Cek status pesanan
```

---

## 🚀 Cara Menjalankan di XAMPP / Laragon

### Langkah 1 — Persiapan
- Pastikan **XAMPP** atau **Laragon** sudah terinstall
- Aktifkan **Apache** dan **MySQL**

### Langkah 2 — Copy Project
```
Salin folder /project ke:
  XAMPP  : C:\xampp\htdocs\project
  Laragon: C:\laragon\www\project
```

### Langkah 3 — Buat Database
1. Buka browser → `http://localhost/phpmyadmin`
2. Klik **"New"** → beri nama: `ud_aneka_rupa`
3. Klik **"Go"**
4. Pilih database `ud_aneka_rupa` → klik tab **"Import"**
5. Pilih file `database.sql` → klik **"Go"**

### Langkah 4 — Konfigurasi (jika perlu)
Edit file `/config/config.php`:
```php
define('DB_HOST', 'localhost');   // biasanya tidak perlu diubah
define('DB_USER', 'root');        // username MySQL (XAMPP: root)
define('DB_PASS', '');            // password MySQL (XAMPP: kosong)
define('DB_NAME', 'ud_aneka_rupa');
define('APP_URL', 'http://localhost/project');  // sesuaikan nama folder
```

### Langkah 5 — Buat Folder Upload
Pastikan folder ini ada dan dapat ditulis:
```
/project/assets/images/produk/
```
Jika belum ada, buat manual atau akan dibuat otomatis saat upload gambar.

### Langkah 6 — Jalankan
- **Website**: `http://localhost/project`
- **Admin Panel**: `http://localhost/project/admin`

---

## 🔑 Akun Login Admin

| Username | Password  |
|----------|-----------|
| `admin`  | `admin123`|

> **Catatan**: Password di database tersimpan dalam format hash PHP `password_hash()`.
> Untuk mengganti password, jalankan di phpMyAdmin:
> ```sql
> UPDATE admin SET password = '$2y$10$...' WHERE username = 'admin';
> ```
> Atau ganti manual menggunakan `password_hash('passwordbaru', PASSWORD_DEFAULT)`.

---

## ✨ Fitur yang Tersedia

### Frontend (Pelanggan)
- ✅ Landing page modern dengan hero section
- ✅ Daftar produk dengan search & filter kategori
- ✅ Pagination produk
- ✅ Detail produk dengan produk terkait
- ✅ Form pemesanan dengan validasi
- ✅ Cek status pesanan dengan progress tracker
- ✅ Animasi loading, hover, dan scroll reveal
- ✅ Navbar sticky dengan efek scroll
- ✅ SweetAlert notifikasi sukses pemesanan
- ✅ Responsive mobile-friendly

### Admin Panel
- ✅ Login dengan session management
- ✅ Dashboard dengan statistik & chart ringkas
- ✅ CRUD Produk (tambah/edit/hapus + upload gambar)
- ✅ Kelola pesanan dengan filter status & search
- ✅ Update status pesanan via SweetAlert
- ✅ Data pelanggan otomatis dari histori pesanan
- ✅ Alert stok rendah di dashboard
- ✅ Pagination semua tabel

---

## 🗄️ Struktur Database

### Tabel `produk`
| Kolom        | Tipe          | Keterangan              |
|--------------|---------------|-------------------------|
| id           | INT PK AI     | Primary key             |
| nama_produk  | VARCHAR(150)  | Nama produk             |
| deskripsi    | TEXT          | Deskripsi produk        |
| harga        | DECIMAL(10,2) | Harga per kemasan       |
| stok         | INT           | Jumlah stok             |
| berat_gram   | INT           | Berat kemasan (gram)    |
| gambar       | VARCHAR(255)  | Nama file gambar        |
| kategori     | ENUM          | original/pedas/manis/gurih |
| status       | ENUM          | aktif/nonaktif          |
| created_at   | TIMESTAMP     | Waktu dibuat            |
| updated_at   | TIMESTAMP     | Waktu diupdate          |

### Tabel `pesanan`
| Kolom          | Tipe          | Keterangan              |
|----------------|---------------|-------------------------|
| id             | INT PK AI     | Primary key             |
| kode_pesanan   | VARCHAR(20)   | Kode unik (ORD-...)     |
| nama_pelanggan | VARCHAR(100)  | Nama pemesan            |
| no_hp          | VARCHAR(20)   | Nomor HP/WA             |
| alamat         | TEXT          | Alamat pengiriman       |
| id_produk      | INT FK        | Relasi ke tabel produk  |
| jumlah         | INT           | Jumlah yang dipesan     |
| total_harga    | DECIMAL(12,2) | Total pembayaran        |
| catatan        | TEXT          | Catatan pelanggan       |
| status         | ENUM          | menunggu/diproses/dikirim/selesai/dibatalkan |
| created_at     | TIMESTAMP     | Waktu pesan             |
| updated_at     | TIMESTAMP     | Waktu update status     |

### Tabel `admin`
| Kolom      | Tipe         | Keterangan              |
|------------|--------------|-------------------------|
| id         | INT PK AI    | Primary key             |
| nama       | VARCHAR(100) | Nama lengkap admin      |
| username   | VARCHAR(50)  | Username login (UNIQUE) |
| password   | VARCHAR(255) | Password (hashed)       |
| created_at | TIMESTAMP    | Waktu dibuat            |

---

## 🛠️ Teknologi

| Teknologi    | Versi   | Keterangan                    |
|--------------|---------|-------------------------------|
| PHP          | 7.4+    | Backend (Native, tanpa framework) |
| MySQL        | 5.7+    | Database                      |
| Bootstrap    | 5.3.0   | CSS Framework (CDN)           |
| Font Awesome | 6.4.0   | Icons (CDN)                   |
| SweetAlert2  | 11.x    | Notifikasi popup (CDN)        |
| Google Fonts | —       | Poppins + Playfair Display    |

---

## 🎨 Desain

- **Warna Utama**: Orange `#F97316` + Kuning `#FBBF24`
- **Font**: Poppins (body) + Playfair Display (judul)
- **Tema**: Modern Food / Warm & Friendly
- **Animasi**: Loading overlay, scroll reveal, hover cards, float cards

---

## ⚠️ Troubleshooting

**Koneksi DB gagal?**
→ Pastikan MySQL aktif, nama DB benar di `config.php`

**Gambar tidak tampil?**
→ Buat folder `assets/images/produk/` dan beri izin write (chmod 755)

**Password admin tidak bisa login?**
→ Jalankan di phpMyAdmin:
```sql
UPDATE admin SET password = 'admin123' WHERE username = 'admin';
```
Lalu login dengan password `admin123` (mode plaintext fallback aktif)

**Session tidak bekerja?**
→ Pastikan tidak ada output sebelum `session_start()` di `config.php`
