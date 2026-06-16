<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'user_id', 'report_ref', 'client_request_ref', 'date_reported', 'date_received',
        'client_name', 'project', 'project_client', 'contractor', 'consultant',
        'concrete_supplier', 'mix_grade', 'slump_mm', 'air_content', 'sampling_method',
        'other_information', 'pour_location', 'total_cubes', 'date_of_casting',
        'sampled_by', 'sampling_location', 'sampling_cert_ref', 'compaction_method',
        'lab_curing_method', 'test_method', 'density_method', 'volume_determination',
        'test_location', 'method_variation', 'cubes_delivered_by', 'dimensions',
        'nominal_size', 'removal_of_fins', 'curing_at_lab', 'tested_by',
        'report_type', 'structural_reference',
    ];

    protected $casts = [];

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class)->orderBy('sort_order');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
