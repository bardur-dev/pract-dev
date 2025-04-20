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
        return $query
            ->when($filterDTO->user_id, fn($q) => $q->where('user_id', $filterDTO->user_id))
            ->when($filterDTO->search, fn($q) => $q->where('name', 'like', "%{$filterDTO->search}%"))
            ->when($filterDTO->status, function ($q) use ($filterDTO) {
                if (TaskStatus::tryFrom($filterDTO->status)) {
                    $q->where('status', $filterDTO->status);
                }
            });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
