<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_report', 'email_time',
    ];


}
