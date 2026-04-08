<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPref extends Model
{
    protected $table = 'exp_huts_user_prefs';

    protected $fillable = [
        'user_id', 'push_enabled',
        'notify_incidents', 'notify_activity_start', 'notify_activity_end',
    ];

    protected function casts(): array
    {
        return [
            'push_enabled'          => 'boolean',
            'notify_incidents'      => 'boolean',
            'notify_activity_start' => 'boolean',
            'notify_activity_end'   => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function forUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['push_enabled' => false, 'notify_incidents' => true, 'notify_activity_start' => true, 'notify_activity_end' => true]
        );
    }
}
