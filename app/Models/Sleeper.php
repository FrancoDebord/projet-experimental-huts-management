<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sleeper extends Model
{
    use SoftDeletes;

    protected $table = 'exp_huts_sleepers';

    protected $fillable = ['site_id', 'name', 'code', 'gender', 'active', 'notes'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function assignments()
    {
        return $this->hasMany(SleeperAssignment::class, 'sleeper_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) { 'M' => 'Homme', 'F' => 'Femme', default => '—' };
    }
}
