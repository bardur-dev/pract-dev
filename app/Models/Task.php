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
            ->when($filterDTO->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when($filterDTO->status, function ($q, $status) {
                if (TaskStatus::tryFrom($status)) {
                    $q->where('status', $status);
                }
            });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
