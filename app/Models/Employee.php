<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use UsingRefs\Model;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'partner_companies';
    protected $fillable = ['uuid', 'user_id', 'company_id', 'hire_date', 'is_active'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(PartnerCompany::class);
    }
}
