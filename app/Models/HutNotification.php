<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HutNotification extends Model
{
    protected $table = 'exp_huts_notifications';

    protected $fillable = ['user_id', 'type', 'title', 'message', 'data', 'url', 'read_at'];

    protected function casts(): array
    {
        return [
            'data'    => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'incident_reported'   => 'fa-triangle-exclamation text-warning',
            'activity_started'    => 'fa-play-circle text-success',
            'activity_ended'      => 'fa-stop-circle text-secondary',
            'activity_completed'  => 'fa-check-circle text-success',
            default               => 'fa-bell text-primary',
        };
    }
}
