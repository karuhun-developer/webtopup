<?php

namespace App\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait WithGetFilterData
{
    public function getDataWithFilter(
        Model|Builder $model,
        array $searchBy = [],
        string $searchBySpecific = '',
        string $orderBy = 'id',
        string $order = 'asc',
        int $paginate = 10,
        string $s = '',
        string $paginateFunction = 'fastPaginate',
    ) {
        $model = $model->where(function ($query) use ($s, $searchBy, $searchBySpecific) {
            if ($searchBySpecific) {
                $query->where($searchBySpecific, 'like', "%$s%");
            } else {
                foreach ($searchBy as $value) {
                    $query->orWhere($value, 'like', "%$s%");
                }
            }
        });

        $model = $model->orderBy($orderBy, $order);

        // $model = $model->latest();

        return $model->{$paginateFunction}($paginate);
    }
}
