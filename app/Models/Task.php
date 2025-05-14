<?php

namespace App\Models;

use App\DTO\TaskFilterDTO;
use App\Enums\TaskStatus;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\ScoutDriverPlus\Searchable;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Elastic\ScoutDriverPlus\Support\Query as ElasticQuery;

class Task extends Model
{
    use HasFactory, Searchable;

    protected $casts = [
        'status' => TaskStatus::class,
    ];

    protected $fillable = [
        'name',
        'user_id',
        'status',
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'user_id' => $this->user_id,
        ];
    }

    public function withSearchableRelations()
    {

    }

    public function searchableAs()
    {
        return 'tasks_index';
    }

    public function scopeFilter(Builder $query, TaskFilterDTO $filterDTO): Builder
    {
        $query->where('user_id', auth()->id());

        if (!$filterDTO->search && !$filterDTO->status) {
            return $query;
        }

        $boolQuery = Query::bool()
            ->must(Query::term()->field('user_id')->value(auth()->id()));

        if ($filterDTO->search) {
            $searchTerm = '*' . strtolower($filterDTO->search) . '*';
            $boolQuery->must(Query::wildcard()
                ->field('name')
                ->value($searchTerm));
        }

        if ($filterDTO->status && TaskStatus::tryFrom($filterDTO->status)) {
            $boolQuery->filter(Query::term()
                ->field('status')
                ->value($filterDTO->status));
        }

        $ids = $this->searchQuery($boolQuery)
            ->execute()
            ->models()
            ->pluck('id')
            ->all();

        return $query->whereIn('id', $ids);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
