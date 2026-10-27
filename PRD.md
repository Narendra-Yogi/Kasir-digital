# PRD — Kasir Digital “Geprek Legend” v2
**Fokus: Sederhanakan untuk UMKM Kecil (Tanpa HPP) + Rekap Hasil Penjualan yang Bagus**

- Tanggal: 7 Oktober 2026
- Versi: 1.0 (Draft untuk disetujui owner)
- Stack eksisting: Laravel 13, PHP ^8.2, Blade + Vite, Chart.js, barryvdh/laravel-dompdf, MySQL/SQLite
- Role eksisting: `admin` (Pemilik) dan `kasir`
- Dokumen ini hasil audit kode aktual di `routes/web.php`, `app/Http/Controllers/*`, `app/Models/*`, `resources/views/*`, `database/migrations/*`.

---

## 1. Ringkasan Eksekutif

Website saat ini adalah aplikasi kasir lengkap dengan fitur resto menengah: POS, Dashboard, Menu + Resep Bahan Baku, perhitungan HPP otomatis, Stok Bahan, Stok Logistik harian, Pengeluaran, Buku Kas Harian (tutup buku), 3 jenis Laporan + Export PDF, dan Kelola Pengguna.

Masalah utama menurut owner:

> HPP terlalu susah untuk UMKM kecil. UMKM kecil hanya butuh **rekap hasil penjualan** yang bagus, simpel, dan mudah dipahami.

Arah v2 yang diusulkan:

1. **Hapus / nonaktifkan konsep HPP** dari seluruh alur (menu, transaksi, laporan, rekap).
2. **Sederhanakan stok** dari 3 sistem menjadi 1 sistem (stok menu saja).
3. **Jadikan “Rekap Hasil” sebagai fitur unggulan**: Omzet – Pengeluaran = Hasil Bersih, dengan tampilan modern, insight otomatis, grafik bagus, dan export cantik.
4. Pertahankan yang sudah bagus: POS cepat, Buku Kas Harian, Pengeluaran manual, Dashboard harian.

Rekomendasi implementasi: **Opsi B — “Mode Sederhana sebagai default, HPP disembunyikan total di v2”** (bukan toggle sementara). Alasannya dijelaskan di §8. Data HPP lama tidak dihapus fisik di database tahap awal (hanya disembunyikan), agar aman rollback.

---

## 2. Konteks: Hasil Audit Fitur Eksisting (As-Is)

### 2.1 Peta navigasi (sidebar aktual)

**Utama (Admin + Kasir bisa akses):**

- Dashboard (`/dashboard` — `DashboardController@index`)
- Kasir / POS (`/kasir` — `PosController@index`, `store`, `struk/{id}`)
- Buku Kas Harian (`/buku-kas-harian` — `index`, `store`, `update` khusus admin)

**Kelola (khusus Admin, `role:admin`):**

- Daftar Menu (`items.*` — `ItemController`)
- Bahan Baku (`ingredients.*` + `restock`, `api.ingredients` — `IngredientController`)
- Kategori Menu (`categories.*`)
- Stok Bahan / Inventories (`inventories.*` — `InventoryController`)
- Laporan Kasir (`reports.index`, `reports.rekap`, `reports.daily` + 3 export PDF + `orders.cancel`)
- Catatan Pengeluaran (`pengeluaran.*` + export PDF)
- Kelola Pengguna (`users.*` + `reset-password`)

**Auth:**

- Login/logout username + password, forgot-password via pertanyaan keamanan (`AuthController`), bukan email.

### 2.2 Alur bisnis utama saat ini

1. Admin input Kategori → Bahan Baku (nama, harga beli, jumlah beli, satuan) → otomatis buat Pengeluaran kategori `bahan`.
2. Admin buat Menu + resep (`item_ingredients`: `quantity_needed`, `unit_used`) → HPP dihitung otomatis: `HPP = Σ(cost_per_unit × quantity_needed)`, dibulatkan ke atas (`Item::recalculateHpp()`).
3. Kasir buka POS → pilih kategori/menu → keranjang → input nama pelanggan, metode `cash/qris`, uang diterima → `PosController@store` validasi stok menu, hitung total, buat `Order` + `OrderDetail` (snapshot `price`, `hpp`, `subtotal`), kurangi `items.stock`, kurangi `ingredients.current_stock` via `deductStock()` dengan konversi satuan (gram↔kg, ml↔liter).
4. Struk ditampilkan (`pos/struk`).
5. Pengeluaran manual/logistik dicatat terpisah (`pengeluaran` kategori `bahan/logistik/lainnya`). Pengeluaran auto dari bahan **tidak bisa diedit/dihapus manual**.
6. Tutup hari: Buku Kas Harian hitung `system_cash_sales`, `system_qris_sales`, `system_expenses`, `starting_cash (= system_expenses saat ini)`, `expected_cash = starting + cash_sales - expenses`, `discrepancy = actual - expected`. Satu tutup buku per hari.
7. Laporan:
   - Penjualan (`reports.index`): omzet, transaksi, batal, rata-rata, cash vs qris, top 5 produk, tren harian, **total HPP + laba kotor**, tabel transaksi + batalkan.
   - Rekap Laba Rugi (`reports.rekap`): pemasukan harian, HPP harian, laba kotor, pengeluaran, laba bersih, margin kotor/bersih, hari terbaik/terburuk, chart bar+line, tabel harian.
   - Harian (`reports.daily`): penjualan, pengeluaran, HPP, laba kotor/bersih, rincian per item + per kasir, banding kemarin (%), stok log + pengeluaran hari itu.
   - Semua bisa export PDF via DomPDF.

### 2.3 Model data eksisting

- `users`: name, username, password, role, security_question/answer.
- `categories`: nama kategori.
- `items`: category_id, name, price, **hpp**, stock, is_available, image, created_by/updated_by + SoftDeletes. Relasi: `itemIngredients`, `ingredients`.
- `ingredients`: name, purchase_price, purchase_quantity, unit, cost_per_unit (auto `price/qty`), current_stock, created_by + SoftDeletes.
- `item_ingredients`: item_id, ingredient_id, quantity_needed, unit_used.
- `orders`: invoice_number (`INV-yymmdd-rand`), user_id, customer_name, total_amount, payment_method (`cash/qris`), cash_received, change, status (`success/cancelled`).
- `order_details`: order_id, item_id, quantity, price, **hpp (snapshot)**, subtotal, notes.
- `pengeluaran`: date, item_name, category (`bahan/logistik/lainnya`), amount, notes, created_by/updated_by, ingredient_id (nullable → penanda auto).
- `inventories`: date, item_name (free text), new_stock, old_stock, sold, remaining_stock (accessor?), created_by.
- `buku_kas_harian`: date (unique per hari), user_id, starting_cash, system_cash_sales, system_qris_sales, system_expenses, actual_cash, discrepancy, notes.
- `jobs`, `cache`, dll standar Laravel.

### 2.4 Tiga sistem stok yang tumpang tindih (sumber kebingungan)

1. `items.stock` — stok porsi menu, dikurangi otomatis saat checkout POS.
2. `ingredients.current_stock` — stok bahan baku, dikurangi otomatis via resep + konversi satuan.
3. `inventories` — log stok manual harian (nama bebas, stok baru/lama/terjual), **tidak terhubung** ke 1 dan 2, hanya tampil di laporan harian.

Untuk UMKM kecil, ini terlalu berat. Lihat §8 untuk simplifikasi.

---

## 3. Problem Statement

| Untuk | Pemilik warung/UMKM kecil (Geprek Legend dan sejenisnya) |
|---|---|
| Yang | Harus input bahan baku, resep per menu, satuan, konversi gram/kg, dan paham HPP/laba kotor vs laba bersih |
| Masalah | (1) Tidak punya timbangan/catatan akurat, harga bahan fluktuatif, (2) Double-input membingungkan: beli bahan → auto pengeluaran + HPP juga muncul di laporan, (3) Laporan menampilkan istilah akuntansi (HPP, margin kotor) yang tidak dipakai sehari-hari |
| Dampak | Malas input, data HPP tidak akurat → laba kotor menyesatkan, akhirnya tidak percaya laporan |
| Kebutuhan sebenarnya | “Hari ini dapat berapa, keluar berapa, sisa bersih berapa, menu apa yang laris, bayar cash/qris berapa” — dalam 1 layar cantik + bisa di-share/export |

**Jobs-to-be-done utama:**

1. Kasir bisa jualan cepat tanpa mikir resep.
2. Owner buka HP malam hari dan langsung paham: omzet, pengeluaran, hasil bersih, tren, menu laris.
3. Owner bisa tunjukkan/cetak rekap ke pasangan/karyawan dengan bangga (hasil bagus).

---

## 4. Goals & Non-Goals v2

### Goals (harus tercapai)

- G1. Hilangkan HPP dari UI, logika, dan laporan. Rumus tunggal: **Hasil Bersih = Total Pemasukan (order success) – Total Pengeluaran**.
- G2. Rekap Hasil Penjualan jadi halaman terbaik: ringkas, visual, insightful, mobile-friendly, export PDF/WA-share cantik.
- G3. Kurangi beban input: tambah menu tanpa resep, catat pengeluaran 1x, stok hanya 1 tempat.
- G4. Tidak merusak POS, Dashboard harian, Buku Kas, dan data historis.
- G5. Migrasi aman: data lama tetap bisa dibaca, tidak ada kehilangan transaksi.

### Non-Goals (tidak dikerjakan di v2)

- Multi-outlet/cabang, multi-gudang, FIFO/average costing, integrasi akuntansi (Jurnal, Accurate), payroll, loyalty/poin, online order, inventaris barcode.
- Ubah sistem auth menjadi email/OTP (tetap username + security question).
- Rewrite frontend ke SPA/React.

### Success metrics (2–4 minggu setelah rilis)

- Waktu tambah menu baru < 60 detik tanpa buka halaman bahan.
- 100% transaksi tanpa error resep/HPP.
- Owner membuka Rekap ≥ 4x/minggu (log view).
- Nol pertanyaan “HPP ini apa?” dari kasir/owner.
- Export/share rekap dipakai ≥ 1x/minggu.

---

## 5. Persona & Hak Akses (tetap)

- **Pemilik/Admin**: semua akses. Fokus ke Rekap, Dashboard, Pengeluaran, Menu, Tutup Buku.
- **Kasir**: hanya Dashboard (read), POS, Buku Kas Harian (buat tutup buku, tidak bisa edit). Tidak akses laporan/rekap/menu/users/pengeluaran.
- Tidak ada perubahan role di v2, hanya penyembunyian menu Bahan Baku & Stok Bahan dari sidebar admin.

---

## 6. Scope v2 (In / Out)

**IN:**

1. Hapus HPP dari Menu, POS snapshot, Laporan Penjualan, Laporan Harian, Rekap, PDF.
2. Arsipkan modul Bahan Baku + Resep + Restock + API ingredients (hidden, tidak delete code tahap 1).
3. Sederhanakan Stok: hanya `items.stock` + `is_available`. Modul `inventories` (Stok Bahan log manual) di-hidden/archive.
4. Upgrade Rekap Hasil (pengganti Rekap Laba Rugi) + Dashboard + Laporan Harian versi tanpa HPP + Export baru.
5. Update Pengeluaran: kategori disederhanakan, hapus auto-pengeluaran dari bahan (karena modul bahan diarsipkan).
6. Update Buku Kas: klarifikasi `starting_cash` (saat ini = total pengeluaran, membingungkan — lihat FR-06).
7. Migrasi data + cleanup view/PDF.

**OUT (tetap seperti sekarang):** login, POS checkout, struk, kategori, users, cancel order, pagination, soft-delete menu.

---

## 7. Functional Requirements Detail

### FR-01 — Menu Tanpa Resep/HPP (penyederhanaan `ItemController`, `items/*`)

- **Saat ini**: form tambah/edit menu wajib pilih bahan + qty + satuan, HPP auto-calculate, tampil margin/laba per unit.
- **Maunya**:
  - Field hanya: Nama, Kategori, Harga Jual, Stok (angka, default 0 atau checkbox “Tanpa batas / selalu tersedia” untuk minuman/jasa?), Foto (opsional), Status tersedia (Ya/Tidak).
  - Hapus section “Resep Bahan”, hapus kolom HPP/margin/laba di `items/index`, `create`, `edit`.
  - Validasi: `price required numeric min 0`, `stock nullable integer min 0`. Tidak ada validasi `ingredients.*`.
  - `syncIngredients()` dan `recalculateHpp()` tidak dipanggil lagi. Tandai `@deprecated` di code, jangan hapus di tahap 1.
  - Cache `pos_categories` tetap di-invalidate saat menu berubah.
- **Acceptance**: tambah menu baru tanpa menyentuh bahan; list menu tidak menampilkan HPP; edit menu lama yang punya HPP tetap tersimpan di DB tapi tidak tampil.

### FR-02 — Arsip Modul Bahan Baku & Resep

- **File terdampak**: `IngredientController`, `Ingredient` model, `ItemIngredient` model, `views/ingredients/*`, `views/items/*` (bagian resep), route `ingredients.*`, `api.ingredients`.
- **Maunya tahap 1 (aman)**: sembunyikan link sidebar “Bahan Baku”, proteksi route return 404/redirect dengan flash “Modul bahan baku dinonaktifkan di mode sederhana”. Jangan drop tabel `ingredients` / `item_ingredients`.
- **Tahap 2 (setelah 1 bulan stabil, persetujuan owner)**: drop kolom `items.hpp`, `order_details.hpp`, drop tabel `ingredients`, `item_ingredients` via migration baru. Hapus file view/controller yang tidak dipakai.
- **Acceptance tahap 1**: tidak ada jalan menuju halaman bahan via UI maupun URL manual (kecuali admin teknis via tinker).

### FR-03 — POS Tanpa Beban HPP (`PosController@store`, `pos/index`, `pos/struk`)

- Hapus snapshot `hpp` saat insert `OrderDetail` (isi 0 atau hapus kolom di tahap 2).
- Hapus blok “Kurangi stok bahan baku berdasarkan resep” (baris 109–132 `PosController`). Hanya `items.decrement(stock)`.
- Tidak ada perubahan validasi pembayaran cash/qris, kembalian, invoice, notes.
- Struk tidak menampilkan HPP (saat ini sudah tidak tampil — pertahankan).
- **Acceptance**: checkout menu yang dulu punya resep tetap sukses tanpa error bahan habis; stok menu berkurang benar; tidak ada baris `pengeluaran` baru yang muncul otomatis.

### FR-04 — Pengeluaran Disederhanakan (`PengeluaranController`, `pengeluaran/*`)

- **Saat ini**: kategori `bahan/logistik/lainnya`, sebagian auto dari bahan (locked edit/delete), summary total + per kategori.
- **Maunya**:
  - Kategori baru yang ramah UMKM: `belanja_harian` (bahan/kebutuhan dapur), `operasional` (gas, listrik, plastik, bensin), `gaji`, `lainnya`. Sediakan migrasi mapping: `bahan → belanja_harian`, `logistik → operasional`, `lainnya → lainnya`.
  - Hapus logika `isAutoGenerated()` / `ingredient_id` lock (karena tidak ada auto lagi). Semua pengeluaran bisa edit/hapus oleh admin. Data auto lama dibuka kuncinya (set `ingredient_id = null` + tambah notes “[arsip auto]”).
  - Form tetap: tanggal, nama, kategori, nominal (format ribuan id-ID), catatan. Tambah filter kategori + search nama di index (improvement kecil, nilai besar).
  - Summary tetap: total + per kategori, tapi label baru.
- **Acceptance**: tambah/edit/hapus pengeluaran tanpa error “otomatis dari bahan”; filter tanggal + kategori bekerja; total cocok dengan Rekap.

### FR-05 — Stok Tunggal (arsip `inventories` / Stok Bahan)

- **Maunya**: satu-satunya stok yang dipakai = `items.stock`. Tambah UX kecil: badge “Habis” (stock ≤0), “Menipis” (stock <5, threshold bisa di-setting env/config, default 5), tombol cepat “+10 / +25 / +50” di list menu (tanpa buka edit).
- Sembunyikan menu sidebar “Stok Bahan” (`inventories.*`) tahap 1. Laporan harian tidak lagi menampilkan tabel `stokBarang` dari `inventories`; ganti dengan “Sisa stok menu saat ini” (top 5 menipis) — lihat FR-07.
- **Acceptance**: kasir tidak bisa checkout melebihi stok (tetap); admin bisa restock dari list menu < 3 klik.

### FR-06 — Buku Kas Harian Diperjelas (bug konseptual wajib fix)

- **Masalah ditemukan**: `starting_cash = system_expenses` (baris 49 & 101 `BukuKasHarianController`). Akibatnya `expected_cash = expenses + cash_sales - expenses = cash_sales`. Ini membuat modal awal fiktif dan selisih selalu `actual - cash_sales`. Membingungkan.
- **Maunya v2**:
  - Tambah field `starting_cash` input manual oleh kasir saat buka/tutup? Minimal: jadikan `starting_cash` = “Modal laci awal hari” input manual (default 0, wajib diisi saat tutup buku pertama kali). Bukan auto = pengeluaran.
  - Rumus tetap: `expected = starting + cash_sales - expenses_tunai?` — putuskan: apakah pengeluaran mengurangi laci tunai saja atau total? Untuk UMKM tunai+QRIS campur, sederhanakan: `expected_cash = starting_cash + cash_sales - expenses_cash_hari_ini` dengan catatan kaki “Pengeluaran dianggap dari laci tunai”. Jika pengeluaran via transfer/QRIS, tambah checkbox “Ambil dari laci tunai?” (default Ya). Ini mencegah selisih misterius.
  - Tampilkan komponen dengan bahasa sederhana di view: “Uang awal laci + Pemasukan tunai − Pengeluaran laci = Seharusnya ada di laci”.
  - Riwayat tetap read-only kecuali admin edit `actual_cash` + notes (tetap).
- **Acceptance**: tutup buku dengan modal 0 + cash 500rb + pengeluaran 50rb → expected 450rb. Selisih benar.

### FR-07 — Rekap Hasil: Fitur Unggulan Baru (pengganti `reports/rekap`)

> Ini menjawab request “rekapannya hasilnya bagus”.

**Lokasi**: route tetap `/reports/rekap` (judul diganti “Rekap Hasil Usaha”), view `reports/rekap.blade.php` rewrite total, PDF `pdf/rekap.blade.php` rewrite.

**Rumus tunggal (tanpa HPP):**

- `Pemasukan (Omzet)` = SUM `orders.total_amount` WHERE `status=success` + rentang tanggal.
- `Pengeluaran` = SUM `pengeluaran.amount` WHERE date di rentang.
- `Hasil Bersih` = Pemasukan − Pengeluaran (bisa negatif → tampil “Minus/Rugi” merah, bukan “Laba negatif”).
- `Margin Bersih %` = Hasil / Pemasukan × 100 (0 jika pemasukan 0).
- Hapus: `totalHpp`, `labaKotor`, `marginKotor`, kolom HPP/Laba kotor di tabel & PDF & chart.

**Filter & preset:**

- Date range `start_date`, `end_date` (default 7 hari terakhir). Preset cepat: Hari ini, Kemarin, 7 hari, 14 hari, Bulan ini, Bulan lalu, Pilih manual. Pertahankan `appends(query)` saat paginasi.
- Filter tambahan v2 (baru): metode bayar (semua/cash/qris), kasir (semua/per user), kategori menu (opsional tahap 2).

**KPI cards (6 kartu, mobile 2 kolom):**

1. Total Pemasukan (besar, format Rp id-ID)
2. Total Pengeluaran (sub: belanja_harian vs operasional vs gaji vs lainnya — mini breakdown)
3. Hasil Bersih (kartu gelap highlight, label “Untung” hijau-emas / “Rugi/Minus” merah + nominal absolut)
4. Margin Bersih % + “x hari untung dari y hari aktif”
5. Transaksi (jumlah + rata-rata per transaksi)
6. Metode: Cash Rp + Qris Rp (donut mini atau progress bar)

**Insight otomatis (baru, nilai “bagus”):**

- Hari terbaik & terendah (berdasarkan Hasil Bersih, bukan laba campur HPP).
- Perbandingan vs periode sebelumnya (contoh: 7 hari ini vs 7 hari sebelumnya): “Naik +12% 🎉” dengan delta Rp.
- Rata-rata harian pemasukan & pengeluaran.
- Menu Top 5 + Menu sepi (bottom 3, qty < threshold) — dari `order_details` group by item.
- Jam ramai? (tahap 2, butuh group by hour — catat sebagai backlog).
- Pesan sederhana bahasa warung: “Sabtu paling rame, Senin paling sepi. Stok ayam tambah Sabtu.” — generate dari data, bukan AI.

**Grafik (Chart.js, tetap):**

- Bar berdampingan: Pemasukan (gelap) vs Pengeluaran (coklat muda) + Line Hasil Bersih (merah/hijau). X = tanggal “d M”. Y format “jt/rb”.
- Tooltip id-ID: “Rp 1.250.000”.
- Grafik kedua (baru): Donut metode bayar + Bar horizontal Top 5 menu. Jaga tinggi mobile (`h-56 lg:h-80` sudah bagus, pertahankan).
- Empty state cantik jika tidak ada data: ilustrasi + CTA “Buka Kasir” / “Catat Pengeluaran”.

**Tabel detail harian:**

- Kolom: Tanggal | Pemasukan | Pengeluaran | Hasil Bersih | Transaksi | Aksi (lihat harian).
- Baris merah muda jika rugi. Badge “Terbaik”. Footer total. Klik tanggal → `/reports/daily?date=YYYY-MM-DD`.
- Hapus kolom HPP/Laba kotor.

**Export & Share (baru, kunci “bagus”):**

- Export PDF portrait A4 rapi: kop usaha (nama, alamat, periode), KPI, grafik (sebagai tabel jika chart tidak render di DomPDF — jangan embed canvas, ganti ringkasan angka), tabel harian, tanda tangan. Nama file `Rekap-Hasil-YYYYMMDD-YYYYMMDD.pdf`.
- Tombol “Salin Ringkasan WA” (baru): copy teks format WhatsApp: “*Rekap 1–7 Okt* 💰 Masuk Rp… • Keluar Rp… • Bersih Rp… • Top: Ayam Geprek (120) 🙏” — implementasi JS clipboard, tanpa backend.
- Pertahankan tombol Export PDF di tabel.

**Acceptance Rekap:**

- Dengan data 7 hari: total pemasukan = sum order success, total pengeluaran = sum pengeluaran, hasil = selisih. Cross-check dengan Dashboard harian + Laporan Penjualan.
- Tidak ada kata “HPP”, “Laba kotor”, “Modal” di halaman/PDF.
- Mobile: kartu tidak overflow, tabel scroll-x, chart terbaca.
- PDF ter-download < 5 detik untuk 30 hari data.

### FR-08 — Dashboard & Laporan Harian Tanpa HPP

- **Dashboard** (`dashboard.blade.php`): sudah bagus (Omzet, Transaksi, Pengeluaran, Laba bersih + chart 7 hari + terlaris + transaksi terbaru). Ganti label “Laba bersih” → “Hasil Bersih” + sublabel “Pemasukan − Pengeluaran”. Hapus referensi HPP (saat ini dashboard sudah bersih — pertahankan).
- **Laporan Penjualan** (`reports/index`): hapus kartu/param `totalHpp`, `labaKotor`, hapus kolom HPP di tabel/PDF, hapus dari `ambilProdukTerlaris` select (`SUM(hpp*qty)`).
- **Laporan Harian** (`reports/daily`): hapus `totalHpp`, `labaKotor`, `rincianBarang.total_hpp`; ganti dengan “Sisa stok menipis” + “Pengeluaran hari ini per kategori”. Rumus `labaBersih` tetap (penjualan − pengeluaran) tapi rename ke `hasilBersih` di view (jaga key lama untuk kompatibilitas tahap 1, tambah alias).
- **Acceptance**: cari kata “hpp” (case-insensitive) di seluruh `resources/views` + `app/Http/Controllers` = 0 hasil setelah tahap 1 (kecuali komentar deprecated).

### FR-09 — Sidebar & Navigasi Baru

- Hapus/hidden: “Bahan Baku”, “Stok Bahan”.
- Urutan baru yang ramah UMKM: Utama → Dashboard, Kasir (POS), Rekap Hasil, Laporan Harian; Kelola → Daftar Menu, Kategori Menu, Catatan Pengeluaran, Buku Kas Harian, Laporan Kasir (arsip detail), Kelola Pengguna.
- Rename: “Rekap Laba Rugi” → “Rekap Hasil”, “Laporan Kasir” → “Rincian Transaksi” (opsional, tapi disarankan agar tidak dobel dengan Rekap).
- Tambah badge kecil di Rekap jika hari ini belum tutup buku (“Belum tutup buku”).

### FR-10 — Users & Auth (tidak berubah, hanya verifikasi)

- Tetap username/password + security question. Pastikan `reset-password` admin dan `forgot-password` mandiri tetap jalan setelah refactor (tidak tersentuh).
- Pastikan middleware `auth` + `role:admin` tetap menutup Rekap/Laporan/Pengeluaran/Menu/Users dari kasir.

---

## 8. Keputusan Desain: Kenapa Hapus Total, Bukan Toggle?

Dipertimbangkan 3 opsi:

- **A. Toggle Mode Sederhana/Lanjutan**: fleksibel, tapi maintenance ganda (setiap laporan harus `if (mode) tampil HPP`), testing 2x, kasir bingung.
- **B. Hapus HPP dari UI (arsip data, hapus fisik tahap 2)** — **dipilih**. Satu kebenaran, satu rumus, code lebih bersih. Data HPP lama tetap di DB tahap 1 untuk audit, tapi tidak tampil. Jika 3 bulan kemudian butuh HPP lagi, restore dari git tag + kolom masih ada.
- **C. Hapus fisik langsung (drop tabel/kolom sekarang)**: bersih tapi berisiko (PDF historis + rollback susah).

Alasan B cocok untuk UMKM: setiap tambahan field resep = tambahan 30–60 detik per menu + butuh timbangan. Tanpa HPP, akurasi “Hasil Bersih” justru naik karena hanya bergantung pada 2 angka yang pasti: uang masuk (POS) dan uang keluar (pengeluaran).

**Catatan double-counting yang diperbaiki B**: saat ini beli bahan dicatat sebagai pengeluaran (cash keluar) DAN HPP dibebankan lagi saat terjual. Secara cash-basis, yang benar untuk UMKM = hanya pengeluaran saat beli. Setelah HPP dihapus, tidak ada lagi “beban ganda” yang membingungkan.

---

## 9. Data & Migrasi

### Tahap 1 (non-destruktif, rilis v2.0)

- Migration `2026_10_XX_000001_add_simple_categories_and_unlock_expenses.php`:
  - Update enum/kategori pengeluaran via data migration (bahan→belanja_harian, logistik→operasional). Kolom `category` string, jadi cukup update data + validasi baru.
  - Set `pengeluaran.ingredient_id = null` untuk semua row auto, tambah prefix notes “[arsip] ”.
  - Tidak drop kolom/tabel apapun.
- Code: tambahkan konstanta `Pengeluaran::KATEGORI = [belanja_harian, operasional, gaji, lainnya]` + map warna/label.
- Tandai `items.hpp`, `order_details.hpp`, `ingredients*`, `item_ingredients*`, `inventories*` sebagai `@deprecated` di PHPDoc.

### Tahap 2 (cleanup, 1 bulan setelah stabil, butuh approval)

- Migration drop: `items.hpp`, `order_details.hpp`, tabel `ingredients`, `item_ingredients`, `inventories` (atau rename ke `_archived_*` dulu 1 rilis).
- Hapus controller/view/route terkait + hapus select `hpp` di semua query aggregate.
- Regenerasi PDF historis tidak diperlukan (PDF lama tetap valid sebagai arsip).

### Rollback

- Tag git `v1-hpp-last` sebelum rilis v2. Jika owner berubah pikiran < 14 hari, revert UI + tampilkan lagi HPP (data masih ada).

---

## 10. UX/UI Spec (agar “hasilnya bagus”)

- **Bahasa**: Indonesia warung, bukan akuntansi. “Pemasukan”, “Pengeluaran”, “Hasil Bersih (Untung/Rugi)”, “Rata-rata per transaksi”. Hindari “Laba”, “Margin” (margin boleh kecil sebagai info sekunder).
- **Format angka**: `Rp 1.250.000` (`number_format(...,0,',','.')`), persen 1 desimal, tanggal `d M Y` (1 Okt 2026).
- **Warna & gaya**: pertahankan tema boutique gelap `#17110c` + krem `#f7f1e6` + aksen merah `#931b13` yang sudah ada. Hasil untung = teks krem di kartu gelap + badge hijau; rugi = merah lembut `#fdf0ee`.
- **Mobile-first**: KPI 2 kolom di HP, grafik `h-56`, tabel `min-w-[560px]` + scroll-x, tombol full-width di filter (`w-full sm:w-auto` sudah benar, pertahankan).
- **Aksesibilitas**: kontras teks ≥ 4.5:1, tombol ≥ 44px di HP, tooltip ramah.
- **Print/PDF**: font Inter/Fraunces fallback DejaVu Sans (DomPDF tidak load Google Fonts otomatis — inline style, hindari Chart.js canvas di PDF, ganti tabel ringkas).
- **Referensi file**: `layouts/app.blade.php` (sidebar), `dashboard.blade.php` (pola kartu + Chart.js yang sudah bagus — jadikan pola untuk Rekap), `reports/rekap.blade.php` (rewrite), `pdf/rekap.blade.php` (rewrite).

---

## 11. Non-Functional Requirements

- Performa: Rekap 30 hari < 1.5 detik (aggregate query, tanpa N+1 — pola `selectRaw + groupBy` yang ada sudah benar, pertahankan). Pagination 20 untuk tabel transaksi.
- Kompatibilitas: Chrome/Android WebView terbaru, layar 360px ke atas. Chart.js via CDN tetap, sediakan fallback tabel jika CDN gagal.
- Keamanan: tetap `auth` + `role:admin`, validasi server-side (nominal strip `.` ribuan, `min:0`), `actual_cash` strip ribuan, proteksi mass-assignment via `$fillable`.
- Audit: `created_by/updated_by`, `user` di pengeluaran/inventory/buku kas tetap. Cancel order hanya ubah status, tidak hapus (audit trail).
- Backup: dump DB sebelum migrasi tahap 1 & 2. Simpan 7 hari.
- Logging: tidak ada perubahan; pastikan `read-log-entries` bersih dari error HPP null.

---

## 12. Roadmap Implementasi (estimasi untuk 1 dev)

**Fase 0 — Persiapan (0.5 hari)**

- Tag `v1-hpp-last`, backup DB, buat branch `v2-simple-rekap`.
- Grep semua `hpp`, `ingredient`, `inventory` → daftar file terdampak (lampiran §14 bisa diregenerasi).

**Fase 1 — Hapus HPP dari UI (1–2 hari)**

- Item form/list, Pos store, Report index/daily/rekap (controller + blade + PDF), Dashboard label.
- Hidden route/menu Bahan Baku + Stok Bahan, proteksi redirect.
- Pengeluaran kategori baru + unlock auto.

**Fase 2 — Rekap Baru yang Bagus (2–3 hari)**

- Rewrite `reports/rekap` + `pdf/rekap` + tombol WA-share + insight (best/worst, vs periode lalu, top/bottom menu, breakdown kategori & metode).
- Update Dashboard label + Laporan Harian tanpa HPP.
- Sidebar baru + empty state + mobile polish. `npm run build` + cek Vite manifest.

**Fase 3 — Buku Kas Fix + Stok Cepat (1 hari)**

- `starting_cash` manual + rumus laci + restock cepat dari list menu.

**Fase 4 — QA & UAT (1 hari)**

- Skenario: jualan cash/qris, kurang bayar, stok habis, batal transaksi, pengeluaran beda kategori, tutup buku selisih, export PDF, rekap 7/30 hari, mobile 360px.
- `php artisan test --compact`, `vendor/bin/pint --dirty`.

**Fase 5 — Rilis + Observasi 2 minggu → Cleanup Tahap 2 (0.5 hari)**

---

## 13. Acceptance Criteria Rilis (checklist PO)

- [ ] Tambah menu tanpa input bahan, tanpa error.
- [ ] Checkout POS sukses, stok menu berkurang, tidak ada pengeluaran auto muncul.
- [ ] Cari “HPP” di UI (Indonesia/Inggris) = 0 hasil.
- [ ] Rekap 7 hari: Pemasukan, Pengeluaran, Hasil, Margin, Transaksi, Cash/Qris, Top 5, Best/Worst, vs periode lalu — semua angka cocok dengan query manual.
- [ ] PDF Rekap + PDF Harian + PDF Penjualan ter-download dan rapi (tanpa kolom HPP).
- [ ] Tombol Salin WA menghasilkan teks valid dan bisa ditempel di WhatsApp.
- [ ] Buku Kas: expected = awal + cash − pengeluaran laci, selisih benar.
- [ ] Kasir tidak bisa buka Rekap/Menu/Pengeluaran via URL (redirect 403).
- [ ] Mobile 360px tidak ada overflow horizontal kecuali tabel (scroll-x disengaja).
- [ ] Tidak ada error di log setelah 3 hari pemakaian.

---

## 14. Lampiran: Daftar File Terdampak (dari audit)

- Hapus/sembunyikan HPP: `app/Models/Item.php` (`hpp`, `margin`, `labaPerUnit`, `hppFromIngredients`, `recalculateHpp`), `app/Models/Ingredient.php`, `app/Models/ItemIngredient.php`, `database/migrations/*hpp*`, `*ingredient*`, `*item_ingredient*`, `app/Http/Controllers/ItemController.php` (`syncIngredients`), `IngredientController.php`, `PosController.php` (snapshot + deductStock), `ReportController.php` (`totalHpp`, `hppHarian`, `total_hpp`, `labaKotor`, `marginKotor`), `DashboardController.php` (label saja), `views/items/*`, `views/ingredients/*`, `views/reports/*`, `views/pdf/*`, `views/dashboard.blade.php`.
- Sederhanakan stok: `InventoryController.php`, `Inventory.php`, `views/inventories/*`, `views/pdf/inventories.blade.php`.
- Pengeluaran: `PengeluaranController.php`, `Pengeluaran.php` (`isAutoGenerated`), `views/pengeluaran/*`, `views/pdf/pengeluaran.blade.php`.
- Buku Kas: `BukuKasHarianController.php`, `BukuKasHarian.php`, `views/buku_kas_harian/index.blade.php`.
- Navigasi: `views/layouts/app.blade.php`, `routes/web.php`.

---

## 15. Pertanyaan untuk Owner (jawab sebelum coding)

1. Setuju HPP dihapus total dari UI (Opsi B) dan data lama diarsipkan, bukan toggle?
2. Kategori pengeluaran baru setuju? (`Belanja Harian, Operasional, Gaji, Lainnya`) — ada yang mau tambah/kurang?
3. `Inventories` (Stok Bahan log manual) boleh disembunyikan? Atau masih dipakai untuk catat stok fisik harian?
4. Buku Kas: modal awal mau input manual tiap hari (disarankan) atau tetap otomatis 0?
5. Rekap: butuh filter per kasir & per metode bayar di v2, atau cukup tanggal dulu?
6. Nama baru: “Rekap Hasil” vs tetap “Rekap Laba Rugi”? Label “Hasil Bersih” vs “Laba Bersih”?
7. Butuh tombol Share WhatsApp di v2, atau PDF saja cukup?

---

**Keputusan yang diminta**: setujui §6–§8 + jawab §15, lalu dev bisa mulai Fase 1–2 langsung tanpa revisi PRD besar.
