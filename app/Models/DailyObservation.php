<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyObservation extends Model
{
    use SoftDeletes;

    protected $table = 'exp_huts_daily_observations';

    protected $fillable = ['session_id', 'hut_id', 'observation_date', 'observation', 'observed_by'];

    protected function casts(): array
    {
        return ['observation_date' => 'date'];
    }

    public function session()
    {
        return $this->belongsTo(UsageSession::class, 'session_id');
    }

    public function hut()
    {
        return $this->belongsTo(Hut::class, 'hut_id');
    }

    public function observer()
    {
        return $this->belongsTo(User::class, 'observed_by');
    }
}
