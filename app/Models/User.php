<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'banjar_id', 'phone', 'address', 'ktp_number', 'avatar_path', 'membership_status', 'review_note', 'reviewed_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->name)) ?: [];
        $initials = array_map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)), array_slice($words, 0, 2));

        return implode('', $initials) ?: '?';
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? asset('storage/'.$this->avatar_path) : null;
    }

    public function labelPeran(): string
    {
        return match ($this->role) {
            'warga' => 'Warga',
            'pengangkut' => 'Pengangkut Sampah',
            default => $this->role,
        };
    }

    public function maskedKtp(): string
    {
        if (! $this->ktp_number) {
            return '—';
        }

        return substr($this->ktp_number, 0, 4).str_repeat('•', 8).substr($this->ktp_number, -4);
    }

    /**
     * Data anggota untuk modal detail (halaman Anggota & Permintaan).
     *
     * @return array<string, mixed>
     */
    public function detailPayload(): array
    {
        $logo = $this->banjar?->logo_path;

        return [
            'nama' => $this->name,
            'peran' => $this->labelPeran(),
            'hp' => $this->phone ?: '—',
            'email' => $this->email,
            'banjar' => $this->banjar?->name ?? 'Tanpa banjar',
            'tanggal' => $this->created_at->locale('id')->translatedFormat('d M Y'),
            'isWarga' => $this->role === 'warga',
            'alamat' => $this->address ?: '—',
            'jangkauan' => [$this->banjar?->name ?? 'Tanpa banjar'],
            'ktp' => $this->maskedKtp(),
            'logoUrl' => $logo ? asset('storage/'.$logo) : null,
            'logoNama' => $logo ? basename($logo) : 'Belum ada logo banjar',
        ];
    }

    /**
     * @return BelongsTo<Banjar, $this>
     */
    public function banjar(): BelongsTo
    {
        return $this->belongsTo(Banjar::class);
    }

    /**
     * @return HasMany<WasteDeposit, $this>
     */
    public function wasteDeposits(): HasMany
    {
        return $this->hasMany(WasteDeposit::class);
    }

    /**
     * @return HasMany<News, $this>
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }
}
