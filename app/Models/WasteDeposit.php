<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'banjar_id', 'detail_lokasi', 'berat_kg', 'status', 'pengangkut_id', 'scheduled_date', 'scheduled_time_slot', 'deposited_on'])]
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
     * Nomor tiket dari inisial banjar + id, contoh: Banjar Kertha Wangi #105 -> BKW-0105.
     */
    public function ticketCode(): string
    {
        $kata = preg_split('/\s+/', trim((string) $this->banjar?->name)) ?: [];

        $inisial = collect($kata)
            ->filter()
            ->take(3)
            ->map(fn (string $k) => mb_strtoupper(mb_substr($k, 0, 1)))
            ->implode('');

        return ($inisial ?: 'KB').'-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
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

    /**
     * @return HasMany<WasteDepositType, $this>
     */
    public function types(): HasMany
    {
        return $this->hasMany(WasteDepositType::class);
    }
}
