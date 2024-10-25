<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskReportFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    public static function boot()
    {
        parent::boot();
        self::creating(function ($taskReportFile) {
            $taskReportFile->user()->associate(auth()->user()->id);
            $taskReportFile->task()->associate(request()->task_id);
        });
        self::updating(function ($taskReportFile) {
            $taskReportFile->user()->associate(auth()->user()->id);
            $taskReportFile->task()->associate(request()->task_id);
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
