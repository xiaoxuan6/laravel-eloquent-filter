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
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
     * The Eloquent field
     *
     * @var array
     */
    protected $filterField = [];

    /**
     * The Eloquent field
     *
     * @var array
     */
    protected $paramsField = [];

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

        $data = $this->parames() + array_flip($this->filterField) + $this->paramsField;

        unset($data['filter']);

        foreach ($data as $key => $v)
        {
            $this->callFunc($key, $v);
        }

        return $this->builder;
    }

    /**
     *
     * @param $builder
     * @param $key
     * @param string $val
     */
    protected function callFunc($key, $val = '')
    {
        if(method_exists($this, $key)) {
            call_user_func([$this, $key], $val);
        }
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

    /**
     * Notes: 添加固定筛选字段
     * Date: 2019/8/19 17:23
     * @param mixed $params
     * @return array
     */
    public function appendField($params)
    {
        $this->filterField = Arr::flatten(func_get_args());

        foreach ($this->filterField as $key => $v)
        {
            if(Str::contains($v, ":"))
            {
                list($start, $end) = explode(':', $v, 2);
                $this->paramsField[$start] = $end;
                unset($this->filterField[$key]);
            }
        }

        return $this;
    }
}