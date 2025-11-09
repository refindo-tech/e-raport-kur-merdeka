<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KokurikulerSubdimension extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function dimension()
    {
        return $this->belongsTo(KokurikulerDimension::class, 'dimension_id');
    }

    public function studentKokurikuler()
    {
        return $this->hasMany(StudentKokurikuler::class, 'subdimension_id');
    }

    public function getDescriptionByLevel($level)
    {
        if ($level === 'berkembang') {
            return $this->berkembang;
        } elseif ($level === 'cakap') {
            return $this->cakap;
        } elseif ($level === 'mahir') {
            return $this->mahir;
        }
        return null;
    }
}

