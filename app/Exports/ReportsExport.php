<?php

namespace App\Exports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $hotelId;
    protected $department;

    public function __construct($startDate, $endDate, $hotelId, $department)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->hotelId = $hotelId;
        $this->department = $department;
    }

    public function collection()
    {
        $query = Report::with(['user.branch', 'items'])
            ->whereBetween('report_date', [$this->startDate, $this->endDate]);

        if ($this->hotelId) {
            $query->whereHas('user', function ($q) {
                $q->where('hotel_id', $this->hotelId);
            });
        }

        if ($this->department) {
            $query->whereHas('user', function ($q) {
                $q->where('department', $this->department);
            });
        }

        $reports = $query->get();

        // Agregasi data per Staf (User)
        $summary = $reports->groupBy('user_id')->map(function ($userReports) {
            $firstReport = $userReports->first();
            $user = $firstReport->user;

            $totalShift = $userReports->count();
            $tepatWaktu = $userReports->where('is_late', false)->where('is_late_submit', false)->count();
            $terlambat = $totalShift - $tepatWaktu;

            // UBAH: Dari Rata-rata (avg) menjadi Total Kumulatif (sum)
            $totalScore = $userReports->sum('total_score');

            $totalSopCompleted = 0;
            $totalExtra = 0;

            foreach ($userReports as $rep) {
                $totalSopCompleted += $rep->items->where('is_additional', false)->where('status', 'completed')->count();
                $totalExtra += $rep->items->where('is_additional', true)->count();
            }

            return [
                'hotel_name' => $user->branch ? $user->branch->name : '-',
                'name' => $user->name,
                'department' => $user->department,
                'total_shift' => $totalShift,
                'tepat_waktu' => $tepatWaktu,
                'terlambat' => $terlambat,
                'total_score' => $totalScore,
                'sop_completed' => $totalSopCompleted,
                'extra_count' => $totalExtra,
            ];
        });

        return collect($summary->values());
    }

    public function headings(): array
    {
        return [
            ['RANGKUMAN PERFORMA & SKOR STAF HOTEL'],
            ['Rentang Waktu:', $this->startDate . ' s/d ' . $this->endDate],
            ['Diunduh Pada:', Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')],
            [],
            [
                'Cabang Hotel',
                'Nama Staf',
                'Departemen',
                'Total Shift Masuk',
                'Tepat Waktu',
                'Terlambat',
                'Total Skor Performa', // Diubah penamaannya
                'Total SOP Selesai',
                'Total Tugas Ekstra'
            ]
        ];
    }

    public function map($row): array
    {
        return [
            $row['hotel_name'],
            $row['name'],
            $row['department'],
            $row['total_shift'],
            $row['tepat_waktu'],
            $row['terlambat'],
            $row['total_score'],
            $row['sop_completed'],
            $row['extra_count']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E2E8F0']]],
        ];
    }
}
