<?php
/**
 * This file is part of PHP CS Fixer.
 *
 * (c) vinhson <15227736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace James\Eloquent\Filter;

use Illuminate\Http\Request;
use Illuminate\Support\{Arr, Str};
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * The Eloquent builder.
     *
     * @var Builder
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
     * @param Builder $builder
     * @return Builder
     */
    public function filterQuery(Builder $builder): Builder
    {
        $this->builder = $builder;

        $data = $this->getRequest() + array_flip($this->filterField) + $this->paramsField;

        foreach ($data as $method => $params) {

            // 处理多条件查询，查询字段名相同
            if (is_numeric($method) && count($params) == 2) {
                $method = $params[0];
                $params = $params[1];
            }

            $params = Str::contains($params, '|') ? explode('|', $params) : [$params];

            $method = Str::camel($method);
            if (method_exists($this, $method) && count($params) == 1) {
                call_user_func_array([$this, $method], $params);
            }
            // 处理指定字段条件查询
            elseif (count($params) == 2) {
                $operator = Str::camel($params[1]);
                switch ($operator) {
                    case 'in':
                        $this->builder->whereIn($method, explode(',', $params[0]));

                        break;
                    case 'or':
                        $this->builder->orWhere($method, $params[0]);

                        break;
                    case 'notIn':
                        $this->builder->whereNotIn($method, explode(',', $params[0]));

                        break;
                    case 'between':
                        $this->builder->whereBetween($method, explode(',', $params[0]));

                        break;
                    case 'notBetween':
                        $this->builder->whereNotBetween($method, explode(',', $params[0]));

                        break;
                    case 'null':
                        $this->builder->whereNull($method);

                        break;
                    case 'notnull':
                        $this->builder->whereNotNull($method);

                        break;
                    case '=':
                    case '>':
                    case '<':
                    case '<>':
                        $this->builder->where($method, $operator, $params[0]);

                        break;
                    case 'like':
                        $this->builder->where($method, $operator, '%' . $params[0] . '%');

                        break;
                    case 'start':
                        $this->builder->where($method, 'like', $params[0] . '%');

                        break;
                    case 'end':
                        $this->builder->where($method, 'like', '%' . $params[0]);

                        break;
                }
            } else {
                $this->builder->where($method, $params);
            }
        }

        return $this->builder;
    }

    /**
     * Fetch all relevant filters from the request.
     *
     * @return array
     */
    protected function getRequest(): array
    {
        return array_filter($this->request->all(), 'strlen');
    }

    /**
     * Notes: 添加固定筛选字段
     * Date: 2019/8/19 17:23
     * @param array $params
     * @return Filter
     */
    public function appendField(array $params): Filter
    {
        $this->filterField = Arr::flatten(func_get_args());

        foreach ($this->filterField as $key => $v) {
            if (Str::contains($v, ':')) {
                $this->paramsField[] = explode(':', $v, 2);
                unset($this->filterField[$key]);
            }
        }

        return $this;
    }
}
