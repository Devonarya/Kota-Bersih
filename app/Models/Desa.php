<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kecamatan_id', 'code', 'name'])]
class Desa extends Model
{
    /**
     * @return BelongsTo<Kecamatan, $this>
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * @return HasMany<Banjar, $this>
     */
    public function banjars(): HasMany
    {
        return $this->hasMany(Banjar::class);
    }
}
