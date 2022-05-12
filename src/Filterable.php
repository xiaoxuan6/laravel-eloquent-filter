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

use Illuminate\Support\Arr;

trait Filterable
{
    protected static $ignoreRequest = [];
    protected static $acceptRequest = [];

    public function scopeIgnoreRequest($query, $ignore)
    {
        self::$ignoreRequest = Arr::wrap($ignore);
    }

    public static function getIgnoreRequest(): array
    {
        return self::$ignoreRequest;
    }

    public function scopeAcceptRequest($query, $accept)
    {
        self::$acceptRequest = Arr::wrap($accept);
    }

    public static function getAcceptRequest(): array
    {
        return self::$acceptRequest;
    }
}
