<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name'])]
class Kabupaten extends Model
{
    /**
     * @return HasMany<Kecamatan, $this>
     */
    public function kecamatans(): HasMany
    {
        return $this->hasMany(Kecamatan::class);
    }
}
