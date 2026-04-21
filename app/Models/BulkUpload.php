<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulkUpload extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'uploaded_by', 'upload_type', 'file_name', 'file_path',
        'original_file_name', 'total_rows', 'processed_rows', 'success_count',
        'error_count', 'error_details', 'status', 'started_at', 'completed_at', 'notes'
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'success_count' => 'integer',
        'error_count' => 'integer',
        'error_details' => 'json',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getProgressPercentage(): int
    {
        if ($this->total_rows === 0) return 0;
        return (int) round(($this->processed_rows / $this->total_rows) * 100);
    }
}
