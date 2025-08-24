<?php

namespace App\Traits;

trait HasActive
{
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
