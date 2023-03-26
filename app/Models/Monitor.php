<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Monitor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'url_or_ip',
        'port',
        'interval',
        'timeout',
        'notify_by_mail',
        'status',
    ];

    /**
     * Get the monitor logs for the monitor.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(MonitorLog::class);
    }

    /**
     * Get the broken links for the monitor.
     */
    public function broken_links(): HasMany
    {
        return $this->hasMany(MonitorLog::class);
    }
}
