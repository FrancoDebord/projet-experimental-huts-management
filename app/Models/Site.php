<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exp_huts_sites';

    protected $fillable = [
        'name', 'village', 'city', 'image_path',
        'latitude', 'longitude', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'float',
            'longitude' => 'float',
        ];
    }

    public function huts()
    {
        return $this->hasMany(Hut::class, 'site_id');
    }

    public function activeHuts()
    {
        return $this->hasMany(Hut::class, 'site_id')->whereNotIn('status', ['abandoned']);
    }

    public function availableHuts()
    {
        return $this->hasMany(Hut::class, 'site_id')->where('status', 'available');
    }

    public function getHutsCountAttribute(): int
    {
        return $this->huts()->count();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'En service' : 'Abandonné';
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->status === 'active'
            ? '<span class="badge bg-success">En service</span>'
            : '<span class="badge bg-secondary">Abandonné</span>';
    }

    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
}
