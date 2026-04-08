<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StateChange extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exp_huts_state_changes';

    protected $fillable = [
        'hut_id', 'previous_status', 'new_status',
        'reason', 'changed_by', 'changed_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function hut()
    {
        return $this->belongsTo(Hut::class, 'hut_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    private static array $labels = [
        'available' => 'Disponible',
        'in_use'    => 'En utilisation',
        'damaged'   => 'Endommagée',
        'abandoned' => 'Abandonnée',
    ];

    public function getPreviousStatusLabelAttribute(): string
    {
        return self::$labels[$this->previous_status] ?? $this->previous_status;
    }

    public function getNewStatusLabelAttribute(): string
    {
        return self::$labels[$this->new_status] ?? $this->new_status;
    }
}
