<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id', 'executed_by', 'status', 'parameters', 'file_path',
        'record_count', 'started_at', 'completed_at', 'error_message'
    ];

    protected $casts = [
        'parameters' => 'json',
        'record_count' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(CustomReport::class);
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function getDuration(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInSeconds($this->completed_at);
        }
        return null;
    }

    public function markAsCompleted(string $filePath, int $recordCount): void
    {
        $this->status = 'completed';
        $this->file_path = $filePath;
        $this->record_count = $recordCount;
        $this->completed_at = now();
        $this->save();
    }

    public function markAsFailed(string $error): void
    {
        $this->status = 'failed';
        $this->error_message = $error;
        $this->completed_at = now();
        $this->save();
    }
}
