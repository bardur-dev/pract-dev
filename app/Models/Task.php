<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Models\Scopes\TaskScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;


class Task extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => TaskStatus::class,
    ];

    protected $fillable = [
        'name',
        'user_id',
        'status',
    ];

    public function scopeFilter(Builder $query, ?string $search, ?string $status): Builder
    {
        return $query->withGlobalScope('filter', new TaskScope($search, $status));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
