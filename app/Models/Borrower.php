<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\BelongsToOrganization;

class Borrower extends Model
{
    use BelongsToOrganization, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::make()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'organization_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'national_id',
        'address',
        'city',
        'state',
        'country',
        'occupation',
        'employer',
        'monthly_income',
        'credit_score',
        'kyc_status',
        'kyc_documents',
        'photo',
        'notes',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'kyc_documents' => 'array',
        'monthly_income' => 'decimal:2',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function guarantors()
    {
        return $this->hasMany(Guarantor::class);
    }
}
