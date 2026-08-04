<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReportItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::deleting(function ($item) {
            if ($item->before_image) {
                Storage::disk('public')->delete($item->before_image);
            }
            if ($item->after_image) {
                Storage::disk('public')->delete($item->after_image);
            }
        });
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
