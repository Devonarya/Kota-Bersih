<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kabupaten_id', 'code', 'name'])]
class Kecamatan extends Model
{
    /**
     * @return BelongsTo<Kabupaten, $this>
     */
    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class);
    }

    /**
     * @return HasMany<Desa, $this>
     */
    public function desas(): HasMany
    {
        return $this->hasMany(Desa::class);
    }
}
