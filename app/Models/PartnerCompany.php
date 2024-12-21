<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerCompany extends Model
{
    use SoftDeletes;

    protected $table = 'partner_companies';
    protected $fillable = ['uuid', 'name', 'cnpj'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
