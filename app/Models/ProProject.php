<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProProject extends Model
{
    protected $table = 'pro_projects';

    protected $fillable = [];

    protected function casts(): array
    {
        return [
            'date_debut_previsionnelle' => 'date',
            'date_debut_effective'      => 'date',
            'date_fin_previsionnelle'   => 'date',
            'date_fin_effective'        => 'date',
        ];
    }

    public function projectUsages()
    {
        return $this->hasMany(ProjectUsage::class, 'project_id');
    }

    public function hutUsages()
    {
        return $this->hasMany(ProjectUsage::class, 'project_id')->with('hut.site');
    }

    public function expHutsActivities()
    {
        return $this->hasMany(StudyActivity::class, 'project_id')
            ->where('study_activity_name', 'Experimental Huts');
    }

    public function getStageLabelAttribute(): string
    {
        return match ($this->project_stage) {
            'not_started' => 'Non démarré',
            'in progress' => 'En cours',
            'suspended'   => 'Suspendu',
            'completed'   => 'Terminé',
            'archived'    => 'Archivé',
            'NA'          => 'N/A',
            default       => $this->project_stage,
        };
    }

    public function getStageColorAttribute(): string
    {
        return match ($this->project_stage) {
            'not_started' => 'secondary',
            'in progress' => 'primary',
            'suspended'   => 'warning',
            'completed'   => 'success',
            'archived'    => 'dark',
            default       => 'secondary',
        };
    }

    public function getDurationDaysAttribute(): ?int
    {
        $start = $this->date_debut_effective ?? $this->date_debut_previsionnelle;
        $end   = $this->date_fin_effective   ?? $this->date_fin_previsionnelle;
        if (!$start || !$end) return null;
        return $start->diffInDays($end) + 1;
    }

    public function getDaysElapsedAttribute(): int
    {
        $start = $this->date_debut_effective ?? $this->date_debut_previsionnelle;
        $end   = $this->date_fin_effective   ?? $this->date_fin_previsionnelle;
        if (!$start || !$end) return 0;
        if (Carbon::today()->lt($start)) return 0;
        if (Carbon::today()->gt($end))   return $this->duration_days ?? 0;
        return Carbon::today()->diffInDays($start) + 1;
    }

    public function getDaysRemainingAttribute(): int
    {
        $end = $this->date_fin_effective ?? $this->date_fin_previsionnelle;
        if (!$end) return 0;
        if (Carbon::today()->gt($end)) return 0;
        return Carbon::today()->diffInDays($end);
    }

    public function getProgressPercentAttribute(): int
    {
        $duration = $this->duration_days;
        if (!$duration || $duration === 0) return 0;
        return min(100, (int) round(($this->days_elapsed / $duration) * 100));
    }
}
