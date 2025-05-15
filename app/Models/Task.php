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
        $boolQuery = Query::bool()
            ->must(Query::term()->field('user_id')->value(auth()->id()));

        $boolQuery->when($filterDTO->search, function ($query) use ($filterDTO) {
            $searchTerm = strtolower($filterDTO->search);
            return $query->must(Query::bool()
                ->should(Query::wildcard()
                    ->field('name')
                    ->value("*{$searchTerm}*"))
                ->should(Query::match()
                    ->field('name')
                    ->query($searchTerm)
                    ->fuzziness('AUTO'))
                ->minimumShouldMatch(1));
        });

        $boolQuery->when($filterDTO->status && TaskStatus::tryFrom($filterDTO->status),
            function ($query) use ($filterDTO) {
                return $query->filter(Query::term()
                    ->field('status')
                    ->value($filterDTO->status));
            }
        );

        $ids = $this->searchQuery($boolQuery)
            ->execute()
            ->models()
            ->pluck('id')
            ->all();

        return $query->whereIn('id', $ids ?: [null]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
