<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrokenLink extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'url',
        'monitor_id',
    ];

    /**
     * Get the monitor that owns the monitor log.
     */
    public function monitor()
    {
        return $this->hasOne(Monitor::class);
    }
}
