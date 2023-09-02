<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionMethod;
use ReflectionParameter;

use function call_user_func;
use function call_user_func_array;

abstract class Filter
{
    /**
     * The request instance.
     *
     * @var Request
     */
    protected Request $request;

    /**
     * The builder instance.
     *
     * @var Builder
     */
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
            call_user_func([$this, 'default']);
        }

        foreach ($this->filters() as $name => $value) {
            $methodName = Str::camel($name);
            $value = array_filter([$value]);
            if ($this->shouldCall($methodName, $value)) {
                call_user_func_array([$this, $methodName], $value);
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

    /**
     * Make sure the method should be called.
     *
     * @param string $methodName
     * @param array $value
     * @return bool
     */
    protected function shouldCall(string $methodName, array $value): bool
    {
        if (!method_exists($this, $methodName)) {
            return false;
        }

        $method = new ReflectionMethod($this, $methodName);
        /** @var ReflectionParameter $parameter */
        $parameter = Arr::first($method->getParameters());

        if ($value) {
            return $method->getNumberOfParameters() > 0;
        }

        return null === $parameter || $parameter->isDefaultValueAvailable();
    }
}
