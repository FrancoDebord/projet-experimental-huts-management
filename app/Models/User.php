<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom', 'prenom', 'email', 'password', 'role',
        'telephone', 'personnel_id', 'active', 'type_user',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'active'            => 'boolean',
        ];
    }

    public function hutNotifications()
    {
        return $this->hasMany(HutNotification::class, 'user_id')->orderBy('created_at', 'desc');
    }

    public function unreadNotificationsCount(): int
    {
        return $this->hutNotifications()->unread()->count();
    }

    public function prefs()
    {
        return $this->hasOne(UserPref::class, 'user_id');
    }

    public function getPrefsAttribute(): UserPref
    {
        return UserPref::forUser($this->id);
    }

    public function getNameAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
