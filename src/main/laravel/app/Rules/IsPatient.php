<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class IsPatient implements Rule
{
    public static function new(): self
    {
        return new self;
    }

    public function passes($attribute, $value)
    {
        return User::patient()->where('id', $value)->exists();
    }

    public function message()
    {
        return 'No such patient available';
    }
}
