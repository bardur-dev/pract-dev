<?php

namespace App\Models;

use App\DTO\TaskFilterDTO;
use App\Enums\TaskStatus;
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

    public function scopeFilter(Builder $query, TaskFilterDTO $filterDTO): Builder
    {
        if ($filterDTO->search) {
            $query->where('name', 'like', "%{$filterDTO->search}%");
        }

        if ($filterDTO->status && TaskStatus::tryFrom($filterDTO->status)) {
            $query->where('status', $filterDTO->status);
        }

        return $query;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
