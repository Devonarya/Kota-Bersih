<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['waste_deposit_id', 'jenis_sampah'])]
class WasteDepositType extends Model
{
    /**
     * @return BelongsTo<WasteDeposit, $this>
     */
    public function wasteDeposit(): BelongsTo
    {
        return $this->belongsTo(WasteDeposit::class);
    }
}
