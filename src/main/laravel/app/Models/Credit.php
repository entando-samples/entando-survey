<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

// this is not a eloquent model but for better consistency
class Credit
{
    public static function update(?string $content = '')
    {
        return Storage::put('credit.txt', $content);
    }

    public static function get()
    {
        return rescue(fn () => Storage::get('credit.txt'), '', false) ?? '';
    }

    public static function delete()
    {
        return rescue(fn () => Storage::delete('credit.txt'), '', false);
    }
}
