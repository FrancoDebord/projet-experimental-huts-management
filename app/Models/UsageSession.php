<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class UsageSession extends Model
{
    use SoftDeletes;

    protected $table = 'exp_huts_usage_sessions';

    protected $fillable = [
        'project_id', 'phase_name', 'date_start', 'date_end',
        'notes', 'status', 'notifications_sent_start', 'notifications_sent_end', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_start' => 'date',
            'date_end'   => 'date',
            'notifications_sent_start' => 'boolean',
            'notifications_sent_end'   => 'boolean',
        ];
    }

    public function project()
    {
        return $this->belongsTo(ProProject::class, 'project_id');
    }

    public function projectUsages()
    {
        return $this->hasMany(ProjectUsage::class, 'session_id');
    }

    public function huts()
    {
        return $this->hasManyThrough(Hut::class, ProjectUsage::class, 'session_id', 'id', 'id', 'hut_id');
    }

    public function sleeperAssignments()
    {
        return $this->hasMany(SleeperAssignment::class, 'session_id');
    }

    public function dailyObservations()
    {
        return $this->hasMany(DailyObservation::class, 'session_id')->orderBy('observation_date');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDurationInDaysAttribute(): int
    {
        return $this->date_start->diffInDays($this->date_end) + 1;
    }

    public function getDatesAttribute(): array
    {
        $dates = [];
        $current = $this->date_start->copy();
        while ($current->lte($this->date_end)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }
        return $dates;
    }

    public function getDaysElapsedAttribute(): int
    {
        if (Carbon::today()->lt($this->date_start)) return 0;
        if (Carbon::today()->gt($this->date_end))   return $this->duration_in_days;
        return Carbon::today()->diffInDays($this->date_start) + 1;
    }

    public function getDaysRemainingAttribute(): int
    {
        if (Carbon::today()->gt($this->date_end)) return 0;
        if (Carbon::today()->lt($this->date_start)) return $this->duration_in_days;
        return Carbon::today()->diffInDays($this->date_end);
    }

    public function getProgressPercentAttribute(): int
    {
        $dur = $this->duration_in_days;
        if ($dur === 0) return 100;
        return min(100, (int) round(($this->days_elapsed / $dur) * 100));
    }

    public function getCurrentStatusAttribute(): string
    {
        $today = Carbon::today();
        if ($this->status === 'cancelled') return 'cancelled';
        if ($this->status === 'completed') return 'completed';
        if ($today->lt($this->date_start))  return 'planned';
        if ($today->gt($this->date_end))    return 'completed';
        return 'active';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->current_status) {
            'planned'   => 'Planifiée',
            'active'    => 'En cours',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->current_status) {
            'planned'   => 'info',
            'active'    => 'success',
            'completed' => 'secondary',
            'cancelled' => 'danger',
            default     => 'secondary',
        };
    }
}
