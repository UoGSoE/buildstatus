<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    /** @use HasFactory<\Database\Factories\MachineFactory> */
    use HasFactory;

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function getShortHostnameAttribute()
    {
        return str_replace('.' . config('buildstatus.base_domain'), '', strtolower($this->name));
    }
}
