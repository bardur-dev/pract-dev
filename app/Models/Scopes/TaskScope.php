<?php

namespace App\Models\Scopes;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TaskScope implements Scope
{
    protected ?string $search;
    protected ?string $status;

    public function __construct(?string $search = null, ?string $status = null)
    {
        $this->search = $search;
        $this->status = $status;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if ($this->search) {
            $builder->where('name', 'like', "%{$this->search}%");
        }

        if ($this->status && TaskStatus::tryFrom($this->status)) {
            $builder->where('status', $this->status);
        }
    }
}
