<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Hut extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exp_huts_huts';

    protected $fillable = [
        'site_id', 'number', 'name', 'latitude', 'longitude',
        'status', 'image_path', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'float',
            'longitude' => 'float',
            'number'    => 'integer',
        ];
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function projectUsages()
    {
        return $this->hasMany(ProjectUsage::class, 'hut_id');
    }

    public function stateChanges()
    {
        return $this->hasMany(StateChange::class, 'hut_id')->orderBy('changed_at', 'desc');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class, 'hut_id')->orderBy('incident_date', 'desc');
    }

    public function currentUsage()
    {
        $today = Carbon::today()->toDateString();
        return $this->projectUsages()
            ->where('date_start', '<=', $today)
            ->where('date_end', '>=', $today)
            ->first();
    }

    public function isAvailableForPeriod(string $start, string $end): bool
    {
        if (in_array($this->status, ['damaged', 'abandoned'])) {
            return false;
        }
        return !$this->projectUsages()
            ->where('date_start', '<=', $end)
            ->where('date_end', '>=', $start)
            ->exists();
    }

    public function getUnavailabilityReason(string $start, string $end): ?string
    {
        if ($this->status === 'damaged')   return 'Endommagée';
        if ($this->status === 'abandoned') return 'Abandonnée';

        $usage = $this->projectUsages()
            ->with('project')
            ->where('date_start', '<=', $end)
            ->where('date_end', '>=', $start)
            ->first();

        if ($usage) {
            $code = $usage->project?->project_code ?? "Projet #{$usage->project_id}";
            $phase = $usage->phase_name ? " ({$usage->phase_name})" : '';
            return "Utilisée par {$code}{$phase}";
        }
        return null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available' => 'Disponible',
            'in_use'    => 'En utilisation',
            'damaged'   => 'Endommagée',
            'abandoned' => 'Abandonnée',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available' => 'success',
            'in_use'    => 'primary',
            'damaged'   => 'warning',
            'abandoned' => 'secondary',
            default     => 'secondary',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        $color = $this->status_color;
        $label = $this->status_label;
        return "<span class=\"badge bg-{$color}\">{$label}</span>";
    }

    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
}
