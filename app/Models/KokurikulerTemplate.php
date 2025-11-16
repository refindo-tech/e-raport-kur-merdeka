<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KokurikulerTemplate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function getByLevel(string $level): ?self
    {
        return static::where('level', $level)->first();
    }
}

