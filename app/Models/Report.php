<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::deleting(function ($report) {
            $report->items->each(function ($item) {
                $item->delete();
            });
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(ReportItem::class);
    }

    public function scoreBreakdown(): array
    {
        $standardItems = $this->items->where('is_additional', 0);
        $totalStandard = $standardItems->count();
        $pendingStandard = $standardItems->where('status', 'pending')->count();
        $completedStandard = $standardItems->whereIn('status', ['completed', 'verified'])->count();
        $validDenominator = $totalStandard - $pendingStandard;

        $baseScore = $validDenominator > 0
            ? ($completedStandard / $validDenominator) * 100
            : ($totalStandard > 0 ? 100 : 0);

        $bonusScore = $this->items->where('is_additional', 1)
            ->whereIn('status', ['completed', 'verified'])
            ->count() * 10;

        $penalty = ($this->is_late ? 15 : 0) + ($this->is_late_submit ? 15 : 0);

        $baseAfterPenalty = max(0, $baseScore - $penalty);
        $totalFinal = (int) round($baseAfterPenalty + $bonusScore);

        return [
            'base_score' => (int) round($baseScore),
            'bonus_score' => $bonusScore,
            'penalty' => $penalty,
            'total_score' => $totalFinal,
        ];
    }
}
