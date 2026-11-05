# Arsitektur Aplikasi Kasir Digital "Geprek Legend" v2

Dokumen ini dibuat untuk presentasi project. Tidak terlalu teknis, cukup menunjukkan bagaimana setiap fitur berjalan dari layar pengguna sampai ke database.

---

## 1. Gambar Besar (Big Picture)

Aplikasi ini adalah **sistem kasir digital untuk UMKM kecil** (warung makan / gedeg geprek). Pengguna utamanya dua orang: **Pemilik (Admin)** dan **Kasir**.

Setiap fitur mengikuti alur yang sama:

```
Pengguna (Browser)
        ↓ (URL / klik tombol)
Routes (web.php)
        ↓ (mengarah ke fungsi di)
Controller
        ↓ (mengambil/mengubah data via)
Model (Eloquent) ↔ Database (MySQL/SQLite)
        ↓ (mengembalikan data ke)
View (Blade HTML)
        ↓ (ditampilkan di layar pengguna)
```

**Teknologi yang dipakai:**
- **Backend:** Laravel 13 (PHP 8.3)
- **Frontend:** Blade + Tailwind CSS + Chart.js + Vite
- **Database:** MySQL atau SQLite
- **Export PDF:** barryvdh/laravel-dompdf
- **Auth:** Username + Password + Pertanyaan Keamanan (bukan email)

---

## 2. Struktur Folder Singkat

```
app/
├── Http/Controllers/   ← Logika bisnis (mana data yang diambil, mana yang disimpan)
├── Models/             ← Representasi tabel database (Item, Order, Pengeluaran, dst)
└── Providers/

routes/
└── web.php             ← Daftar semua halaman (URL) aplikasi

resources/views/        ← Halaman HTML (Blade)
├── layouts/            ← Template wrapper (sidebar, navbar)
├── dashboard.blade.php ← Halaman utama
├── pos/                ← Halaman kasir (POS)
├── reports/            ← Laporan & Rekap Hasil
├── pdf/                ← Template cetak PDF
├── pengeluaran/        ← Catatan pengeluaran
├── buku_kas_harian/    ← Buku kas harian
├── items/              ← Kelola menu
├── categories/         ← Kelola kategori
├── users/              ← Kelola pengguna
└── auth/               ← Login, lupa password

database/
└── migrations/         ← Riwayat pembuatan tabel database
```

---

## 3. Flow Setiap Fitur Utama

Berikut adalah alur kerja setiap fitur dari mulai pengguna klik sampai data tersimpan atau ditampilkan.

### A. Login & Autentikasi

**Pengguna:** Pemilik atau Kasir

```
Layar Login (username + password)
        ↓
AuthController@authenticate
        ↓
Cek ke database tabel `users`
        ↓
Jika cocok → buat session login, arahkan ke halaman sesuai role
        ↓
Admin → bisa buka semua halaman
Kasir → hanya boleh buka Dashboard, Kasir/POS, Buku Kas Harian
```

**Fitur tambahan:** Lupa password → jawab pertanyaan keamanan → reset password.

---

### B. Kasir / POS (Tempat Jualan)

**Pengguna:** Kasir (Admin juga bisa)

```
Kasir buka halaman /kasir
        ↓
PosController@index ambil daftar kategori + menu dari database
        ↓
Tampilkan di layar: kategori tombol, daftar menu dengan harga & stok
        ↓
Kasir pilih menu → masukkan keranjang → atur jumlah
        ↓
Input nama pelanggan, pilih metode bayar (Cash / QRIS), uang diterima
        ↓
Klik "Checkout"
        ↓
PosController@store:
  1. Validasi stok mencukupi (LOCK FOR UPDATE agar tidak oversell)
  2. Hitung total, kembalian
  3. Simpan transaksi ke tabel `orders` (status: success)
  4. Simpan detail setiap item ke tabel `order_details`
  5. Kurangi stok menu di tabel `items`
  6. Commit semua sekaligus (transaksi database)
        ↓
Tampilkan struk pembelian
```

**Aturan stok:** Jika stok menu tidak cukup, checkout ditolak dengan pesan error.

---

### C. Dashboard (Ringkasan Hari Ini)

**Pengguna:** Admin dan Kasir (buka-buka saja, tidak bisa ubah)

```
DashboardController@index
        ↓
Ambil dari database:
  - Jumlah pesanan hari ini
  - Total pendapatan hari ini
  - Jumlah pesanan dibatalkan
  - Total pengeluaran hari ini
  - Perbandingan dengan kemarin (naik/turun %)
  - Grafik penjualan 7 hari terakhir
  - 5 produk terlaris hari ini
  - 5 transaksi terbaru
        ↓
Tampilkan di layar dashboard dengan kartu-kartu angka + grafik
```

**Rumus sederhana:** Pendapatan − Pengeluaran = Hasil Bersih

---

### D. Rekap Hasil Usaha (Fitur Unggulan)

**Pengguna:** Hanya Admin

```
Admin buka /reports/rekap
        ↓
Pilih rentang tanggal (default 7 hari terakhir)
        ↓
ReportController@rekap → ambilDataRekap()
        ↓
Query database:
  - Pemasukan = jumlah semua order sukses dalam rentang tanggal
  - Pengeluaran = jumlah semua catatan pengeluaran dalam rentang tanggal
  - Hasil Bersih = Pemasukan − Pengeluaran
  - Margin Bersih % = Hasil / Pemasukan × 100
  - Hari terbaik & terburuk
  - Perbandingan vs periode sebelumnya
  - Menu terlaris (top 5) & menu sepi (bottom 3)
  - Pemasukan per metode bayar (Cash vs QRIS)
  - Pengeluaran per kategori (Belanja Harian, Operasional, Gaji, Lainnya)
        ↓
Tampilkan:
  - 6 KPI kartu (Pemasukan, Pengeluaran, Hasil Bersih, Margin, Transaksi, Metode Bayar)
  - Grafik bar + line (Pemasukan vs Pengeluaran vs Hasil)
  - Tabel harian detail
  - Insight otomatis (contoh: "Sabtu paling rame, Senin paling sepi")
        ↓
Tombol aksi: Export PDF, Salin Ringkasan WhatsApp
```

**Rumus tunggal (tanpa rumus akuntansi rumit):**
```
Hasil Bersih = Total Uang Masuk − Total Uang Keluar
```

---

### E. Laporan Harian

**Pengguna:** Hanya Admin

```
Pilih tanggal tertentu
        ↓
ReportController@dailyReport → ambilDataHarian()
        ↓
Ambil semua transaksi dan pengeluaran pada tanggal itu
        ↓
Tampilkan:
  - Total penjualan, total pengeluaran, hasil bersih
  - Rincian per item (menu apa saja yang laku)
  - Rincian per kasir (siapa saja yang transaksi)
  - Pengeluaran per kategori
  - Perbandingan dengan kemarin
  - Peringatan stok menipis (stok < 5)
        ↓
Tombol Export PDF
```

---

### F. Laporan Penjualan / Rincian Transaksi

**Pengguna:** Hanya Admin

```
Pilih rentang tanggal
        ↓
ReportController@index
        ↓
Tampilkan daftar semua transaksi dengan paginasi (20 per halaman)
        ↓
Setiap baris: nomor invoice, tanggal, kasir, pelanggan, total, metode bayar, status
        ↓
Tombol aksi: Lihat detail, Batalkan transaksi (kembalikan stok)
        ↓
Ringkasan: omzet, jumlah transaksi, batal, rata-rata, cash vs qris, top 5 produk
        ↓
Tombol Export PDF
```

**Pembatalan transaksi:** Stok yang dijual dikembalikan ke menu, status berubah jadi "cancelled" (data tidak dihapus untuk audit).

---

### G. Kelola Menu (Daftar Menu)

**Pengguna:** Hanya Admin

```
ItemController (index, create, edit, store, update, destroy)
        ↓
Input: Nama menu, Kategori, Harga Jual, Stok, Foto (opsional), Status Tersedia/Tidak
        ↓
Simpan ke tabel `items` dan `categories`
        ↓
Tampilkan di daftar menu dengan badge stok (Habis / Menipis / Tersedia)
        ↓
Tombol cepat: +10 / +25 / +50 stok tanpa buka form edit
```

**Catatan:** Tidak ada input resep/bahan baku lagi (mode sederhana v2).

---

### H. Kategori Menu

**Pengguna:** Hanya Admin

```
CategoryController (index, create, edit, store, update, destroy)
        ↓
Input: Nama kategori (contoh: Makanan, Minuman, Snack)
        ↓
Simpan ke tabel `categories`
        ↓
Kategori dipakai saat menambah menu dan menampilkan di layar kasir
```

---

### I. Catatan Pengeluaran

**Pengguna:** Hanya Admin

```
PengeluaranController (index, create, edit, store, update, destroy)
        ↓
Input: Tanggal, Nama pengeluaran, Kategori (Belanja Harian / Operasional / Gaji / Lainnya), Nominal, Catatan
        ↓
Simpan ke tabel `pengeluaran`
        ↓
Tampilkan daftar semua pengeluaran dengan total dan breakdown per kategori
        ↓
Semua bisa diedit dan dihapus oleh admin
        ↓
Tombol Export PDF
```

---

### J. Buku Kas Harian (Tutup Buku)

**Pengguna:** Kasir (buat), Admin (edit)

```
BukuKasHarianController (index, store, update)
        ↓
Kasir input: Uang awal laci (modal awal), Pemasukan tunai, Pengeluaran dari laci, Uang aktual di laci
        ↓
Rumus: Diharapkan ada di laci = Uang Awal + Pemasukan Tunai − Pengeluaran Laci
        ↓
Selisih = Uang Aktual − Diharapkan ada
        ↓
Simpan ke tabel `buku_kas_harian` (satu catatan per hari)
        ↓
Admin bisa edit uang aktual dan catatan nanti
```

---

### K. Kelola Pengguna

**Pengguna:** Hanya Admin

```
UserController (index, create, edit, store, update, destroy, reset-password)
        ↓
Kelola daftar kasir dan admin
        ↓
Role: admin atau kasir
        ↓
Reset password pakai pertanyaan keamanan (bukan email)
```

---

## 4. Diagram Alur Data (Data Flow)

```
                    +-------------------+
                    |   PENGGUNA        |
                    |  (Browser/Web)    |
                    +--------+----------+
                             |
                             | HTTP Request (GET/POST/PUT/DELETE)
                             v
                    +-------------------+
                    |   ROUTES          |
                    |  routes/web.php   |
                    +--------+----------+
                             |
                             | Cocokkan URL → Controller
                             v
                    +-------------------+
                    |  CONTROLLERS      |
                    |  (Logic Bisnis)   |
                    +--------+----------+
                             |
              +--------------+--------------+
              |                             |
              v                             v
     +----------------+             +----------------+
     |   MODELS       |             |   DATABASE     |
     |  (Eloquent)    |             |  (MySQL/SQLite)|
     +----------------+             +----------------+
              |                             ^
              |                             |
              +--------------+--------------+
                             |
                             v
                    +-------------------+
                    |   VIEWS           |
                    |  (Blade HTML)     |
                    +--------+----------+
                             |
                             v
                    +-------------------+
                    |   LAYAR PENGGUNA  |
                    +-------------------+
```

---

## 5. Tabel Database Utama

| Tabel | Fungsi | Kolom Penting |
|-------|--------|---------------|
| `users` | Data kasir & admin | name, username, password, role |
| `categories` | Kategori menu | name |
| `items` | Menu / daftar jualan | category_id, name, price, stock, is_available |
| `orders` | Transaksi penjualan | invoice_number, user_id, total_amount, payment_method, status |
| `order_details` | Detail item per transaksi | order_id, item_id, quantity, price, subtotal |
| `pengeluaran` | Catatan uang keluar | date, item_name, category, amount |
| `buku_kas_harian` | Tutup buku harian | date, starting_cash, actual_cash, discrepancy |
| `inventories` | (Arsip) Log stok lama | date, item_name, new_stock, old_stock |

---

## 6. Hak Akses (Role)

| Fitur | Admin | Kasir |
|-------|-------|-------|
| Login | ✅ | ✅ |
| Dashboard | ✅ | ✅ (read-only) |
| Kasir / POS | ✅ | ✅ |
| Rekap Hasil | ✅ | ❌ |
| Laporan Harian | ✅ | ❌ |
| Laporan Penjualan | ✅ | ❌ |
| Kelola Menu | ✅ | ❌ |
| Kelola Kategori | ✅ | ❌ |
| Catatan Pengeluaran | ✅ | ❌ |
| Buku Kas Harian | ✅ (edit) | ✅ (buat tutup buku) |
| Kelola Pengguna | ✅ | ❌ |
| Export PDF | ✅ | ❌ |

---

## 7. Ringkasan Rumus Penting

Untuk presentasi, cukup ingat 3 rumus ini:

1. **Hasil Bersih** = Pemasukan (order sukses) − Pengeluaran
2. **Margin Bersih %** = (Hasil Bersih ÷ Pemasukan) × 100
3. **Selisih Laci** = Uang Aktual di Laci − (Uang Awal + Pemasukan Tunai − Pengeluaran Laci)

---

## 8. Alur Presentasi (Rekomendasi Urutan)

Saat presentasi, bisa diikuti urutan ini:

1. **Login** → tampilkan dua role (admin vs kasir)
2. **Dashboard** → ringkasan hari ini (omzet, pengeluaran, hasil)
3. **Kasir / POS** → demo jualan, cetak struk, stok berkurang otomatis
4. **Rekap Hasil** → fitur unggulan, grafik, export PDF, share WhatsApp
5. **Laporan Harian** → detail satu hari, rincian item & kasir
6. **Kelola Menu** → tambah menu, atur stok, kategori
7. **Catatan Pengeluaran** → catat uang keluar, breakdown per kategori
8. **Buku Kas Harian** → tutup buku, cek selisih laci
9. **Kelola Pengguna** → tambah kasir/admin

---

**Catatan:** Arsitektur ini menggunakan pola **MVC (Model-View-Controller)** standar Laravel, sehingga mudah dipelihara dan dikembangkan oleh tim yang paham Laravel.
