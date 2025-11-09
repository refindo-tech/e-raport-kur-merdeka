<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KokurikulerDimension extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function subdimensions()
    {
        return $this->hasMany(KokurikulerSubdimension::class, 'dimension_id');
    }
}

