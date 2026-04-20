<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDocument extends Model
{
    protected $fillable = [
        'loan_id',
        'document_type',
        'file_path',
        'uploaded_by',
        'description',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
