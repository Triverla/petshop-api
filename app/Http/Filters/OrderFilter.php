<?php


namespace App\Http\Filters;


use App\Models\ClientWrapper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class OrderFilter extends Filter
{

    public function dateRange(array $value): Builder
    {
        if (isset($value['from']) && isset($value['to'])) {
           return $this->builder->whereBetween(
                'created_at',
                [
                    Carbon::parse($value['from']),
                    Carbon::parse($value['to']),
                ]
            );
        }


        return $this->builder->whereDate('created_at', $value['from'] ?? $value['to']);;
    }

    /**
     *
     * @param string|null $value
     * @return Builder
     */
    public function fixRange(string $value = null): Builder
    {
        if (isset($value) && $value === 'today') {
            return $this->builder->whereDate('created_at', Carbon::today());
        }

        if (isset($value) && $value === 'yesterday') {
            return $this->builder->whereDate('created_at', Carbon::yesterday());
        }

        if (isset($value) && $value === 'monthly') {
            return $this->builder->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()]);
        }

        if (isset($value) && $value === 'yearly') {
            return $this->builder->whereBetween('created_at', [Carbon::now()->subYear(), Carbon::now()]);
        }

        return $this->builder->whereDate('created_at', Carbon::today());
    }

    /**
     * Filter orders by order ID.
     *
     * @param string|null $value
     * @return Builder
     */
    public function orderUuid(string $value = null): Builder
    {
        if (isset($value)) {
            return $this->builder->where('uuid', $value);
        }

        return $this->builder;
    }

    /**
     * Filter orders by Customer.
     *
     * @param string|null $value
     * @return Builder
     */
    public function customerUuid(string $value = null): Builder
    {
        if (isset($value)) {
            return $this->builder->where('user_id', $value);
        }

        return $this->builder;
    }
}
