<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exp_huts_incidents';

    protected $fillable = [
        'hut_id', 'project_usage_id', 'project_id',
        'title', 'description', 'incident_date', 'severity', 'status',
        'reported_by', 'resolved_by', 'resolved_at', 'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'resolved_at'   => 'datetime',
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

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getSeverityLabelAttribute(): string
    {
        return match ($this->severity) {
            'low'      => 'Faible',
            'medium'   => 'Moyen',
            'high'     => 'Élevé',
            'critical' => 'Critique',
            default    => $this->severity,
        };
    }

    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'low'      => 'success',
            'medium'   => 'warning',
            'high'     => 'danger',
            'critical' => 'dark',
            default    => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open'        => 'Ouvert',
            'in_progress' => 'En traitement',
            'resolved'    => 'Résolu',
            'closed'      => 'Clôturé',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open'        => 'danger',
            'in_progress' => 'warning',
            'resolved'    => 'success',
            'closed'      => 'secondary',
            default       => 'secondary',
        };
    }
}
