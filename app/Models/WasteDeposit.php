<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'banjar_id', 'jenis_sampah', 'keterangan', 'berat_kg', 'status', 'pengangkut_id', 'scheduled_date', 'scheduled_time_slot', 'deposited_on'])]
class WasteDeposit extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deposited_on' => 'date',
            'scheduled_date' => 'date',
            'berat_kg' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Banjar, $this>
     */
    public function banjar(): BelongsTo
    {
        return $this->belongsTo(Banjar::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function pengangkut(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengangkut_id');
    }
}
