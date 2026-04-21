<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Blacklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'scope', 'blacklistable_type', 'blacklistable_id',
        'identifier_type', 'identifier_value', 'name', 'reason', 'details',
        'blacklisted_by', 'removed_by', 'removed_at', 'removal_reason'
    ];

    protected $casts = [
        'details' => 'json',
        'removed_at' => 'datetime',
    ];

    public function blacklistable(): MorphTo
    {
        return $this->morphTo();
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function blacklistedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blacklisted_by');
    }

    public function removedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'removed_by');
    }

    public static function check(string $type, string $value, ?int $organizationId = null): ?self
    {
        $query = self::where('identifier_type', $type)
            ->where('identifier_value', $value)
            ->whereNull('removed_at');

        if ($organizationId) {
            $query->where(function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId)
                    ->orWhere('scope', 'global');
            });
        }

        return $query->first();
    }
}
