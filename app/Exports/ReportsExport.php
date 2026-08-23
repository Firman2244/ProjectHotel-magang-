<?php

namespace App\Exports;

use App\Models\Report;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
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
        return User::with('branch:id,name')
            ->where('role', '!=', 'admin')
            ->when($this->hotelId, fn($q) => $q->where('hotel_id', $this->hotelId))
            ->when($this->department, fn($q) => $q->where('department', $this->department))
            ->addSelect([
                'total_shift' => Report::selectRaw('COUNT(*)')
                    ->whereColumn('user_id', 'users.id')
                    ->whereBetween('report_date', [$this->startDate, $this->endDate]),
                'tepat_waktu' => Report::selectRaw('COUNT(*)')
                    ->whereColumn('user_id', 'users.id')
                    ->whereBetween('report_date', [$this->startDate, $this->endDate])
                    ->where('is_late', false)
                    ->where('is_late_submit', false),
                'total_score' => DB::table('point_histories')
                    ->selectRaw('COALESCE(SUM(points), 0)')
                    ->whereColumn('user_id', 'users.id')
                    ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']),
                'sop_completed' => DB::table('report_items')
                    ->join('reports', 'reports.id', '=', 'report_items.report_id')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('reports.user_id', 'users.id')
                    ->whereBetween('reports.report_date', [$this->startDate, $this->endDate])
                    ->where('report_items.is_additional', false)
                    ->whereIn('report_items.status', ['completed', 'verified']),
                'extra_count' => DB::table('report_items')
                    ->join('reports', 'reports.id', '=', 'report_items.report_id')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('reports.user_id', 'users.id')
                    ->whereBetween('reports.report_date', [$this->startDate, $this->endDate])
                    ->where('report_items.is_additional', true)
            ])
            ->has('reports')
            ->get()
            ->map(function ($user) {
                $totalShift = (int) $user->total_shift;
                $tepatWaktu = (int) $user->tepat_waktu;

                return [
                    'hotel_name' => $user->branch?->name ?? '-',
                    'name' => $user->name,
                    'department' => $user->department,
                    'total_shift' => $totalShift,
                    'tepat_waktu' => $tepatWaktu,
                    'terlambat' => max(0, $totalShift - $tepatWaktu),
                    'total_score' => (float) $user->total_score,
                    'sop_completed' => (int) $user->sop_completed,
                    'extra_count' => (int) $user->extra_count,
                ];
            });
    }

    public function headings(): array
    {
        return [
            ['RANGKUMAN PERFORMA & SKOR STAF HOTEL'],
            ['Rentang Waktu:', $this->startDate . ' s/d ' . $this->endDate],
            ['Diunduh Pada:', Carbon::now()->timezone('Asia/Jakarta')->format('d M Y, H:i')],
            [],
            ['Cabang Hotel', 'Nama Staf', 'Departemen', 'Total Shift Masuk', 'Tepat Waktu', 'Terlambat', 'Total Skor Performa', 'Total SOP Selesai', 'Total Tugas Ekstra']
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
            $row['extra_count'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2:A3')->getFont()->setBold(true);

                $headerRange = 'A5:' . $lastColumn . '5';
                $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0284C7');

                $dataRange = 'A5:' . $lastColumn . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $sheet->getStyle('D6:' . $lastColumn . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D6:' . $lastColumn . $lastRow)->getNumberFormat()->setFormatCode('0');
            },
        ];
    }
}
