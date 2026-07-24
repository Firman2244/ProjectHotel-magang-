<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::deleting(function ($report) {
            foreach ($report->items as $item) {
                if ($item->before_image) {
                    Storage::disk('public')->delete($item->before_image);
                }
                if ($item->after_image) {
                    Storage::disk('public')->delete($item->after_image);
                }

                $item->delete();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ReportItem::class);
    }
}
