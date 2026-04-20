<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guarantor extends Model
{
    protected $fillable = [
        'borrower_id',
        'name',
        'phone',
        'email',
        'relationship',
        'national_id',
        'address',
    ];

    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }
}
