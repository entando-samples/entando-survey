<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasAdminRole{
    public function scopeAdmin(Builder $q)
    {
        return $q->where("role", User::ADMIN);
    }
}
