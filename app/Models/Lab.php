<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    /** @use HasFactory<\Database\Factories\LabFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'notes',
    ];

    public function machines(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Machine::class);
    }
}
