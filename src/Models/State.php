<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public $timestamps = false;
    
    public function getAll(?string $filter = null, ?string $region = null): Collection
    {
        return static::query()
                            ->when(
                                $filter,
                                fn(Builder $builder): Builder => $builder->where('name', 'LIKE', $filter),
                            )
                            ->when(
                                $region,
                                fn(Builder $builder): Builder => $builder->where('region', '=', $region),
                            )
                            ->orderBy('name')
                            ->get();
    }

    public static function seed(): void
    {
        $jsonString = file_get_contents(
            __DIR__.'/../../resources/json/states.min.json',
        );
        $json = json_decode($jsonString, true);
        foreach ($json as $data) {
            static::query()->create($data);
        }
    }

    public static function random(array $columns = ['*']): self
    {
        return static::query()->inRandomOrder()->first($columns);
    }
}
