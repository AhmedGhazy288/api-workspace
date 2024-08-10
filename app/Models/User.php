<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Ramsey\Collection\Collection;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'username',
        'photo',
        'password',
        "status",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        "id",
        "status",
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    //TYPES
    //0 => ADMIN
    //1 => NORMAL
    const TYPES_NAMES = [
        '0' => 'Admin',
        '1' => 'Normal'
    ];

    const TYPES_ROUTES = [
        '0' => 'admin',
        '1' => 'user',
    ];

    const NAMES_TYPES = [
        'Admin' => '0',
        'Normal' => '1',
    ];


    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')->using(UserPermission::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class, 'user_id');
    }


    public function getPhotoUrlAttribute()
    {
        return empty($this->photo) ?
            '' :
            Storage::disk("users")->url($this->photo);
    }

    public function getIsAdminAttribute()
    {
        return $this->type == self::NAMES_TYPES['Admin'];
    }

    public function getIsNormalAttribute()
    {
        return $this->type == self::NAMES_TYPES['Normal'];
    }

    public function getPermissionsListAttribute()
    {
        return isset($this->permissions) ? $this->permissions->map(
            static fn($e) => [
                'id' => $e->id,
                'name' => $e->name,
            ])
            : collect();
    }


    public function has($permissionKey): bool
    {
        return $this->isAdmin ||
            (
                $this->isNormal &&
                $this->permissionsList->where('name', $permissionKey)->first()
            );
    }
}
