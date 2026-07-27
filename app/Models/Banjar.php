<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'desa', 'description', 'family_count'])]
class Banjar extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function wasteDeposits(): HasMany
    {
        return $this->hasMany(WasteDeposit::class);
    }
}
