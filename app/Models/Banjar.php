<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'desa', 'description', 'logo_path', 'family_count'])]
class Banjar extends Model
{
    public function logoUrl(): ?string
    {
        return $this->logo_path ? asset('storage/'.$this->logo_path) : null;
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<WasteDeposit, $this>
     */
    public function wasteDeposits(): HasMany
    {
        return $this->hasMany(WasteDeposit::class);
    }
}
