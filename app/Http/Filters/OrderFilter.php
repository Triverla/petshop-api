<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OrderFilter extends Filter
{
    public function dateRange(array $value): Builder
    {
        $from = isset($value['from']) ? Carbon::parse($value['from']) : null;
        $to = isset($value['to']) ? Carbon::parse($value['to']) : null;

        if ($from && $to) {
            return $this->builder->whereBetween('created_at', [$from, $to]);
        }

        return $from
            ? $this->builder->whereDate('created_at', $from)
            : $this->builder->whereDate('created_at', Carbon::today());
    }

    public function fixRange(?string $value = null): Builder
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $now = Carbon::now();

        return match ($value) {
            'today' => $this->builder->whereDate('created_at', $today),
            'yesterday' => $this->builder->whereDate('created_at', $yesterday),
            'monthly' => $this->builder->whereBetween('created_at', [$now->subMonth(), $now]),
            'yearly' => $this->builder->whereBetween('created_at', [$now->subYear(), $now]),
            default => $this->builder,
        };
    }

    public function orderUuid(?string $value = null): Builder
    {
        return isset($value) ? $this->builder->where('uuid', $value) : $this->builder;
    }

    public function customerUuid(?string $value = null): Builder
    {
        return isset($value) ? $this->builder->where('user_id', $value) : $this->builder;
    }
}
