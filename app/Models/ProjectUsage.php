<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ProjectUsage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exp_huts_project_usages';

    protected $fillable = [
        'session_id', 'hut_id', 'project_id', 'study_activity_id',
        'phase_name', 'date_start', 'date_end', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_start' => 'date',
            'date_end'   => 'date',
        ];
    }

    public function hut()
    {
        return $this->belongsTo(Hut::class, 'hut_id');
    }

    public function project()
    {
        return $this->belongsTo(ProProject::class, 'project_id');
    }

    public function session()
    {
        return $this->belongsTo(UsageSession::class, 'session_id');
    }

    public function studyActivity()
    {
        return $this->belongsTo(StudyActivity::class, 'study_activity_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDurationInDaysAttribute(): int
    {
        return $this->date_start->diffInDays($this->date_end) + 1;
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
        if ($this->duration_in_days === 0) return 100;
        return min(100, (int) round(($this->days_elapsed / $this->duration_in_days) * 100));
    }

    public function getStatusAttribute(): string
    {
        $today = Carbon::today();
        if ($today->lt($this->date_start)) return 'upcoming';
        if ($today->gt($this->date_end))   return 'completed';
        return 'active';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'upcoming'  => 'À venir',
            'active'    => 'En cours',
            'completed' => 'Terminée',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'upcoming'  => 'info',
            'active'    => 'success',
            'completed' => 'secondary',
            default     => 'secondary',
        };
    }
}
