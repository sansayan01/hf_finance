<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'address',
        'phone',
        'email',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function borrowers()
    {
        return $this->hasMany(Borrower::class);
    }

    public function loanProducts()
    {
        return $this->hasMany(LoanProduct::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
