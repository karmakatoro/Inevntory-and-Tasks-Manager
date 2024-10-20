<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($task) {
            $task->project()->associate(request()->project_id);
            $task->user()->associate(auth()->user()->id);
        });
        self::updating(function ($task) {
            $task->project()->associate(request()->project_id);
            $task->user()->associate(auth()->user()->id);
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function project_task()
    {
        return $this->hasMany(ProjectTask::class);
    }
}
