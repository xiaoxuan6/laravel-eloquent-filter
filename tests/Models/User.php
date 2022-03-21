<?php
/**
 * This file is part of PHP CS Fixer.
 *
 * (c) vinhson <15227736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace James\Eloquent\Filter\Tests\Models;

use James\Eloquent\Filter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;
    use Filterable;

    /**
     * @var array|mixed
     */
    protected $guarded = [];

    protected $table = 'users';

    public function book(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
