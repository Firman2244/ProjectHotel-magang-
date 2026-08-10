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
    protected string $startDate;
    protected string $endDate;
    protected ?int $hotelId;
    protected ?string $department;

    public function __construct(string $startDate, string $endDate, ?int $hotelId, ?string $department)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->hotelId = $hotelId;
        $this->department = $department;
    }

    public function collection()
    {
        return Report::with(['user.branch', 'items'])
            ->whereBetween('report_date', [$this->startDate, $this->endDate])
            ->when($this->hotelId, fn($q) => $q->whereHas('user', fn($u) => $u->where('hotel_id', $this->hotelId)))
            ->when($this->department, fn($q) => $q->whereHas('user', fn($u) => $u->where('department', $this->department)))
            ->get()
            ->groupBy('user_id')
            ->map(function ($userReports) {
                $user = $userReports->first()->user;
                $totalShift = $userReports->count();
                $tepatWaktu = $userReports->where('is_late', false)->where('is_late_submit', false)->count();

                $items = $userReports->pluck('items')->flatten();

                return [
                    'hotel_name' => $user->branch?->name ?? '-',
                    'name' => $user->name,
                    'department' => $user->department,
                    'total_shift' => $totalShift,
                    'tepat_waktu' => $tepatWaktu,
                    'terlambat' => $totalShift - $tepatWaktu,
                    'total_score' => $userReports->sum('total_score'),
                    'sop_completed' => $items->where('is_additional', false)->where('status', 'completed')->count(),
                    'extra_count' => $items->where('is_additional', true)->count(),
                ];
            })->values();
    }

    public function headings(): array
    {
        return [
            ['RANGKUMAN PERFORMA & SKOR STAF HOTEL'],
            ['Rentang Waktu:', $this->startDate . ' s/d ' . $this->endDate],
            ['Diunduh Pada:', Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')],
            [],
            ['Cabang Hotel', 'Nama Staf', 'Departemen', 'Total Shift Masuk', 'Tepat Waktu', 'Terlambat', 'Total Skor Performa', 'Total SOP Selesai', 'Total Tugas Ekstra']
        ];
    }

    public function map($row): array
    {
        return array_values($row);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E2E8F0']]],
        ];
    }
}
