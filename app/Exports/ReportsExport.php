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

        return $query->orderBy('report_date', 'desc')->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            ['REKAPITULASI LAPORAN HARIAN STAF'],
            ['Rentang Waktu:', $this->startDate . ' s/d ' . $this->endDate],
            ['Diunduh Pada:', Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')],
            [],
            [
                'Tanggal Laporan',
                'Cabang Hotel',
                'Nama Staf',
                'Departemen',
                'Shift',
                'Jam Datang',
                'Jam Selesai',
                'Status Kepatuhan',
                'Skor Performa',
                'Tugas SOP (Selesai)',
                'Tugas SOP (Pending)',
                'Tugas Ekstra',
                'Catatan Shift',
                'Alasan Kendala'
            ]
        ];
    }

    public function map($report): array
    {
        $clockIn = Carbon::parse($report->created_at)->timezone('Asia/Jakarta')->format('H:i');
        $clockOut = $report->status == 'completed' ? Carbon::parse($report->updated_at)->timezone('Asia/Jakarta')->format('H:i') : '-';

        $statusWaktu = ($report->is_late || $report->is_late_submit) ? 'Terlambat' : 'Tepat Waktu';

        $sopCompleted = $report->items->where('is_additional', false)->where('status', 'completed')->count();
        $sopPending = $report->items->where('is_additional', false)->where('status', 'pending')->count();
        $extraCount = $report->items->where('is_additional', true)->count();

        $obstacles = $report->items->where('status', 'pending')
            ->pluck('obstacle_note')
            ->filter()
            ->implode('; ');

        $hotelName = $report->user->branch ? $report->user->branch->name : '-';

        return [
            Carbon::parse($report->report_date)->format('d/m/Y'),
            $hotelName,
            $report->user->name,
            $report->user->department,
            'Shift ' . $report->shift_id,
            $clockIn,
            $clockOut,
            $statusWaktu,
            $report->total_score,
            $sopCompleted,
            $sopPending,
            $extraCount,
            $report->notes ?? '-',
            $obstacles ?: '-'
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
