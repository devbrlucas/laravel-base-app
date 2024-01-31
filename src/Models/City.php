<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class City extends Model
{
    public $timestamps = false;

    public static function getAll(?string $filter = null, ?int $stateId = null): Collection
    {
        return static::query()
                            ->when(
                                $filter,
                                fn(Builder $builder): Builder => $builder->where('name', 'LIKE', $filter),
                            )
                            ->when(
                                $stateId,
                                fn(Builder $builder): Builder => $builder->where('state_id', '=', $stateId),
                            )
                            ->orderBy('name')
                            ->get();
    }

    public static function seed(): void
    {
        $sql = file_get_contents(
            __DIR__.'/../../resources/sql/cities.sql',
        );
        DB::statement($sql);
    }

    public static function random(array $columns = ['*']): self
    {
        return static::query()->inRandomOrder()->first($columns);
    }
}
