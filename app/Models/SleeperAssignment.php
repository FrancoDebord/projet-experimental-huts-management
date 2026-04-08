<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SleeperAssignment extends Model
{
    use SoftDeletes;

    protected $table = 'exp_huts_sleeper_assignments';

    protected $fillable = ['session_id', 'hut_id', 'sleeper_id', 'assignment_date', 'notes'];

    protected function casts(): array
    {
        return ['assignment_date' => 'date'];
    }

    public function session()
    {
        return $this->belongsTo(UsageSession::class, 'session_id');
    }

    public function hut()
    {
        return $this->belongsTo(Hut::class, 'hut_id');
    }

    public function sleeper()
    {
        return $this->belongsTo(Sleeper::class, 'sleeper_id');
    }
}
