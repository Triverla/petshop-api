<?php

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class Paginator
{

    public function paginateData(Request $request, Builder $builder): LengthAwarePaginator
    {
        $perPage = $request->input('limit', 10);
        $sortBy = $request->input('sortBy', 'id');
        $page = $request->input('page', 1);

        $desc = $request->input('desc') ? 'DESC' : 'ASC';

        return $builder->orderBy($sortBy, $desc)->paginate($perPage, ['*'], 'page', $page);
    }

}
