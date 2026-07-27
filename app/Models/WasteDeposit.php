<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongTo;

class WasteDeposit extends Model
{
    protected function casts(): array
    {
        return [
            'deposited_on' => 'date',
            'schedule_date' => 'date',
            'berat_kg' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function banjar(): BelongsTo
    {
        return $this->belongsTo(Banjar::class);
    }
    
    public function pengankut(): BelongsTo
    {
        return $this->belongTo(User::class, 'pengangkut_id');
    }
}
