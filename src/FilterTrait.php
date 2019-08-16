<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2019/8/16
 * Time: 17:00
 */
namespace James\Eloquent\Filter;

trait FilterTrait
{
    public function scopeFilter($query, Filter $filter)
    {
        return $filter->filter($query);
    }
}