<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'created_by', 'name', 'description', 'report_type',
        'filters', 'columns', 'sorting', 'format', 'frequency', 'schedule_day',
        'schedule_time', 'email_recipients', 'is_shared', 'is_scheduled'
    ];

    protected $casts = [
        'filters' => 'json',
        'columns' => 'json',
        'sorting' => 'json',
        'email_recipients' => 'json',
        'is_shared' => 'boolean',
        'is_scheduled' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(ReportExecution::class, 'report_id');
    }

    public function latestExecution(): BelongsTo
    {
        return $this->belongsTo(ReportExecution::class, 'report_id')
            ->latestOfMany();
    }
}
