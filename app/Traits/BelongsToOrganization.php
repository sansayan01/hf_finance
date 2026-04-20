<?php

namespace App\Traits;

use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganization
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);

        static::creating(function (Model $model) {
            if (Auth::check() && Auth::user()->organization_id) {
                $model->organization_id = Auth::user()->organization_id;
            }
        });
    }

    /**
     * Get the organization that owns the model.
     */
    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }
}
