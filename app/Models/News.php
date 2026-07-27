<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongTo;

class News extends Model
{
    protected function casts(): array
    {
        return[
            'published_at' => 'date',
        ];
    }

    public function author(): BelongTo
    {
        return $this->belongTo(User::class, 'user_id');
    }
}
