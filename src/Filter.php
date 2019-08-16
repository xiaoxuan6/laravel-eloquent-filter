<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2019/8/16
 * Time: 17:04
 */
namespace James\Eloquent\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Filter
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * The Eloquent builder.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * Create a new ThreadFilters instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * the filter.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->parames() as $key => $v)
        {
            if(method_exists($this, $key)) {
                call_user_func([$this, $key], $v);
            }
        }
        return $this->builder;
    }

    /**
     * Fetch all relevant filters from the request.
     *
     * @return array
     */
    protected function parames()
    {
        return array_filter($this->request->all());
    }
}