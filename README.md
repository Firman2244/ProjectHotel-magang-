# Aplikasi Laporan Harian Hotel & Sistem Poin

Ini adalah aplikasi berbasis web yang dibuat khusus untuk mengelola laporan harian staf hotel, mencatat kerusakan (Work Order), dan menghitung skor kinerja staf secara otomatis.

## 🔗 Tautan Akses & Berkas Serah Terima

- **Link Demo (InfinityFree):** [http://laporanharianhotel.infinityfree.io](http://laporanharianhotel.infinityfree.io)
- **Link Repository GitHub:** [github.com/Firman2244/ProjectHotel-magang-](https://github.com/Firman2244/ProjectHotel-magang-)
- **Link Google Drive (ZIP & SQL):** [Google Drive Folder Berkas Serah Terima](https://drive.google.com/drive/folders/1JREjS46DE65wU240Qp_KDhf2i60eUjee?usp=sharing)

---

## 📦 Apa Saja File Serah Terima Ini?

1. **Link Demo (InfinityFree):** Bisa dibuka langsung untuk mencoba aplikasi. (Karena menggunakan server gratis, proses upload foto mungkin agak lambat).
2. **File Google Drive (ZIP & SQL):** Ini adalah **File Utama** yang sudah matang dan siap di-upload ke layanan hosting resmi milik hotel nantinya.
3. **Source Code (GitHub):** Ini adalah file mentah (kode sumber) khusus untuk programmer jika ke depannya aplikasi ingin dikembangkan lagi.

---

## 🚀 Fitur Utama Aplikasi

- **Laporan Harian (SOP):** Karyawan mengisi tugas harian sesuai shift masing-masing.
- **Laporan Kerusakan:** Staf bisa memfoto kerusakan/kendala dan mengirimkannya langsung ke divisi Engineering.
- **Sistem Poin (Leaderboard):** Karyawan yang rajin dan tepat waktu akan mendapatkan poin, dan peringkatnya bisa dilihat langsung oleh semua orang.
- **Kompresi Foto Otomatis:** Foto dari kamera HP otomatis diperkecil ukurannya sebelum masuk ke server agar memori hosting tidak cepat penuh.
- **Ekspor Excel:** Admin bisa mengunduh riwayat laporan karyawan ke dalam format Excel.

---

## 🛠️ Panduan Instalasi (Untuk Pemula / Pengelola Hosting cPanel)

Jika hotel sudah membeli layanan Hosting (misal: Hostinger, Niagahoster, dll) dan Domain, ikuti langkah mudah berikut menggunakan file dari **Google Drive**:

1. **Upload File Web:**
   - Login ke cPanel Hosting Anda.
   - Buka menu **File Manager** -> masuk ke folder `public_html`.
   - Upload file `Aplikasi_Laporan.zip` (dari Google Drive) ke sana, lalu klik kanan dan pilih **Extract**.

2. **Upload Database:**
   - Di cPanel, buka menu **MySQL Databases** dan buat database baru (contoh: `db_hotel`). Jangan lupa buat juga Username dan Password-nya.
   - Buka menu **phpMyAdmin**, pilih database yang baru dibuat, lalu pilih tab **Import**.
   - Masukkan file `database_hotel.sql` (dari Google Drive) dan klik Go.

3. **Sambungkan Web dengan Database:**
   - Kembali ke File Manager, cari file bernama `.env` (Jika tidak terlihat, klik Settings di pojok kanan atas dan centang "Show Hidden Files").
   - Klik kanan file `.env` -> pilih **Edit**.
   - Ubah bagian ini sesuai dengan nama database, user, dan password yang baru saja Anda buat di langkah 2:
     ```env
     DB_DATABASE=nama_database_baru_anda
     DB_USERNAME=user_database_baru_anda
     DB_PASSWORD=password_database_baru_anda
     ```
   - Simpan (Save Changes). Selesai! Web sudah bisa diakses melalui domain Anda.

---

## ⚠️ Catatan Keamanan Penting (Harap Dibaca)

1. **Pendaftaran Akun Baru:** 
   Tombol "Register" / daftar mandiri untuk umum telah **dimatikan**. Akun karyawan baru HANYA BISA dibuat oleh Admin dari dalam menu "Manajemen Staf". Ini bertujuan agar orang luar tidak bisa sembarangan masuk ke dalam sistem hotel.
   
2. **Hapus Foto Otomatis:**
   Aplikasi ini dilengkapi fitur hapus foto lama secara otomatis (agar server tidak penuh). Untuk mengaktifkannya, minta pihak hosting Anda untuk menyalakan fitur "Cron Job" yang diarahkan ke sistem Laravel.
   
3. **Zona Waktu:**
   Semua jam keterlambatan sudah dikunci ke Waktu Indonesia Barat (WIB).

---
*Dikembangkan oleh: Firman Maulana Bhagaskara (Tahun 2026).*
