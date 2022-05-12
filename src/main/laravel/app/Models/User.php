<?php

namespace App\Models;

use App\Traits\HasAdminRole;
use App\Traits\HasDoctorRole;
use App\Traits\HasPatientRole;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements \App\Firebase\Notifiable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasPatientRole;
    use HasAdminRole;
    use HasDoctorRole;

    const ADMIN = 'admin';
    const PATIENT = 'patient';
    const DOCTOR = 'doctor';

    protected $fillable = [
        'name',
        'email',
        'password',
        "role",
        "is_alertable",
        "email_report",
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_alertable' => 'boolean',
    ];

    public static function roles()
    {
        return [
            'admin' => 'Admin',
            'doctor' => 'Doctor',
        ];
    }

    public function getNotifiableId(): string
    {
        return 'user-' . $this->id;
    }

    public function scopeBoUser(Builder $q): Builder
    {
        return $q->whereIn('role', [self::ADMIN, self::DOCTOR]);
    }

    public function scopeSearch(Builder $q, $search)
    {
        if (!$search) return $q;

        $pattern = "%" . $search . "%";

        return $q->where("name", "like", $pattern)
            ->orWhere("email", "like", $pattern);
    }

    public function scopeUserData(Builder $q, $userId)
    {
        return $q->where('id', $userId);
    }

    public function messages():HasMany
    {
        return $this->hasMany(Message::class,'sender_id','id');
    }
    
}
