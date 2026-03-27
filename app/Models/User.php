<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\User;
use App\Models\Product;
use App\Models\Project;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function project()
    {
        return $this->hasMany(Project::class);
    }
    public function task()
    {
        return $this->hasMany(Task::class);
    }
    public function project_task()
    {
        return $this->hasMany(Project::class);
    }
    public function task_report_file()
    {
        return $this->hasMany(TaskReportFile::class);
    }
    public function product()
    {
        return $this->hasMany(Product::class);
    }
    public function product_stock()
    {
        return $this->hasMany(ProductStock::class);
    }
}
