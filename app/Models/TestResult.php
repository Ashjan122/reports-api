<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    protected $fillable = [
        'report_id', 'client_ref', 'lab_sample_ref', 'req_test_age', 'act_test_age',
        'date_of_test', 'condition_upon_receipt', 'condition_at_test',
        'dim_l', 'dim_w', 'dim_h', 'mass_kg', 'density',
        'max_load_kn', 'comp_strength', 'type_of_fracture', 'sort_order',
    ];

    protected $casts = [
        'date_of_test' => 'date',
        'dim_l' => 'float',
        'dim_w' => 'float',
        'dim_h' => 'float',
        'mass_kg' => 'float',
        'density' => 'float',
        'max_load_kn' => 'float',
        'comp_strength' => 'float',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
