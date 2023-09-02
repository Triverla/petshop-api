<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ReflectionMethod;
use function call_user_func;
use function call_user_func_array;

abstract class Filter
{
    protected Request $request;
    protected Builder $builder;

    /**
     * Initialize a new filter instance.
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->builder, $name)) {
            return call_user_func_array([$this->builder, $name], $arguments);
        }
    }

    /**
     * Apply the filters on the builder.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        if (empty($this->filters()) && method_exists($this, 'default')) {
            return call_user_func([$this, 'default']);
        }

        foreach ($this->filters() as $name => $value) {
            $methodName = Str::camel($name);
            $value = array_filter([$value]);

            if (method_exists($this, $methodName)) {
                $method = new ReflectionMethod($this, $methodName);
                if ($method->getNumberOfParameters() > 0 || $value) {
                    call_user_func_array([$this, $methodName], $value);
                }
            }
        }

        return $this->builder;
    }

    /**
     * Get all request filters data.
     *
     * @return array
     */
    public function filters(): array
    {
        return $this->request->all();
    }
}
