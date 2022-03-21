<?php
/**
 * This file is part of PHP CS Fixer.
 *
 * (c) vinhson <15227736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace James\Eloquent\Filter\Tests\Filters;

use James\Eloquent\Filter\Filter;
use Illuminate\Database\Eloquent\Builder;

class OauthFilter extends Filter
{
    public function name($name): Builder
    {
        return $this->builder->where('name', $name);
    }
}
