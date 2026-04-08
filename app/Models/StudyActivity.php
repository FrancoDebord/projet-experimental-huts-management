<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyActivity extends Model
{
    protected $table = 'pro_studies_activities';

    protected $fillable = [];

    protected function casts(): array
    {
        return [
            'estimated_activity_date'     => 'date',
            'estimated_activity_end_date' => 'date',
            'actual_activity_date'        => 'date',
        ];
    }

    public function project()
    {
        return $this->belongsTo(ProProject::class, 'project_id');
    }
}
