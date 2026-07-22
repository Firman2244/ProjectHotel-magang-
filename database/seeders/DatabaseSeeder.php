<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Shift;
use App\Models\User;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Shift
        $shift1 = Shift::create(['name' => 'Shift 1', 'start_time' => '07:00:00', 'end_time' => '16:00:00']);
        $shift2 = Shift::create(['name' => 'Shift 2', 'start_time' => '13:00:00', 'end_time' => '22:00:00']);
        $shift3 = Shift::create(['name' => 'Shift 3', 'start_time' => '22:00:00', 'end_time' => '07:00:00']);

        // 2. Buat Data Hotel
        $hotelWahyu = Hotel::create(['name' => 'Hotel Wahyu', 'address' => 'Jl. Wahyu No. 1']);
        $hotelNirwana = Hotel::create(['name' => 'Hotel Nirwana', 'address' => 'Jl. Nirwana No. 2']);

        // 3. Buat Akun Admin (Pak Wahyu)
        User::create([
            'name' => 'Admin Wahyu',
            'email' => 'admin@wahyu.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'hotel_id' => $hotelWahyu->id,
            'shift_id' => $shift1->id,
        ]);

        // 4. Buat Akun Staff/Karyawan Contoh
        User::create([
            'name' => 'Budi Staff (Housekeeping)',
            'email' => 'budi@wahyu.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'Housekeeping',
            'hotel_id' => $hotelWahyu->id,
            'shift_id' => $shift1->id,
        ]);

        // 5. Masukkan Data Task Baku dari 6 Departemen (Pak Ridwan)
        Task::insert([
            // Front Office
            ['department' => 'Front Office', 'name' => 'Briefing pagi dan pembagian tugas', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Mengecek occupancy, arrival, departure, dan in-house guest', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Proses check-in tamu', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Proses check-out tamu', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Menjawab telepon dan WhatsApp reservasi', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Input reservasi ke sistem PMS', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Mengelola pembayaran (cash, transfer, QRIS, kartu)', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Rekonsiliasi kas (cash float)', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Menangani permintaan dan keluhan tamu', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Koordinasi dengan Housekeeping terkait status kamar', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Koordinasi dengan Engineering jika ada kerusakan', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Menjual upgrade kamar dan layanan tambahan', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Update room status pada PMS', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Membuat laporan shift', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Front Office', 'name' => 'Serah terima antar shift', 'created_at' => now(), 'updated_at' => now()],

            // Housekeeping
            ['department' => 'Housekeeping', 'name' => 'Morning briefing', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Pembagian room attendant dan area public area', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Membersihkan kamar check-out', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Membersihkan kamar stay over', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Menyiapkan kamar arrival', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Mengganti linen dan amenities', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Membersihkan area lobby', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Membersihkan toilet umum', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Membersihkan restoran dan meeting room', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Menyapu dan mengepel area publik', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Mengumpulkan linen kotor ke laundry', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Mengisi ulang linen dan amenities', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Melaporkan lost & found', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Melaporkan kerusakan kamar kepada Engineering', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Housekeeping', 'name' => 'Update room status ke Front Office', 'created_at' => now(), 'updated_at' => now()],

            // Food & Beverage Service
            ['department' => 'Food & Beverage Service', 'name' => 'Briefing sebelum operasional', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Menyiapkan restoran sebelum buka', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Menata meja (table setting)', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Menyiapkan buffet breakfast', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Melayani tamu restoran', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Mengambil dan mengantar pesanan', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Menyiapkan room service', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Membersihkan meja setelah tamu selesai', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Membuat minuman sederhana', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Mengisi ulang buffet', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Membersihkan area restoran', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Menghitung stok harian operasional', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Menyiapkan kebutuhan banquet atau meeting', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Closing kasir restoran', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Service', 'name' => 'Membersihkan seluruh peralatan pelayanan', 'created_at' => now(), 'updated_at' => now()],

            // Food & Beverage Product (Kitchen)
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Briefing dapur', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Menerima bahan baku', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Mengecek kualitas bahan makanan', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Menyiapkan bahan (preparation)', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Memasak menu breakfast', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Memasak pesanan à la carte', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Menyiapkan coffee break', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Menyiapkan banquet', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Menjaga kebersihan area dapur', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Mencatat penggunaan bahan', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Melakukan stock opname harian', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Menyimpan bahan sesuai FIFO/FEFO', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Membersihkan peralatan memasak', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Membuang limbah makanan sesuai prosedur', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Food & Beverage Product (Kitchen)', 'name' => 'Menyiapkan kebutuhan untuk operasional esok hari', 'created_at' => now(), 'updated_at' => now()],

            // Engineering
            ['department' => 'Engineering', 'name' => 'Morning briefing', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Patrol seluruh area hotel', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Mengecek listrik', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Mengecek genset', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Mengecek pompa air', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Mengecek panel listrik', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Mengecek AC', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Mengecek water heater', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Mengecek plumbing', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Mengecek pencahayaan', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Menangani work order dari departemen lain', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Perbaikan kerusakan kamar', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Perawatan kolam renang', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Mencatat penggunaan listrik dan air', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Engineering', 'name' => 'Membuat laporan pekerjaan harian', 'created_at' => now(), 'updated_at' => now()],

            // Accounting
            ['department' => 'Accounting', 'name' => 'Briefing singkat', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Menerima setoran kas dari Front Office', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Verifikasi transaksi harian', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Input jurnal harian', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Posting pendapatan hotel', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Rekonsiliasi kas', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Rekonsiliasi bank (jika ada transaksi masuk)', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Verifikasi invoice supplier', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Input hutang usaha', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Menyiapkan pembayaran supplier', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Mengecek petty cash', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Mengontrol biaya operasional', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Menyusun Daily Revenue Report', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Filing dokumen keuangan', 'created_at' => now(), 'updated_at' => now()],
            ['department' => 'Accounting', 'name' => 'Menyiapkan laporan untuk manajemen', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
