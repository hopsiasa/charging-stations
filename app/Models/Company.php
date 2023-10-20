<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['parent_company_id', 'name'];

    public function stations(): HasMany
    {
        return $this->hasMany(Station::class);
    }
}
