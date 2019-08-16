## Installation

This package can be installed through Composer.

```
composer require james.xue/laravel-eloquent-filter
```

## 使用
~~~
php artisan make:filter Test
~~~

### Model
~~~
<?php

namespace App\Model;

use James\Eloquent\Filter\FilterTrait;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use FilterTrait;
    ……
}
~~~

### TestFilter

~~~
public function code($code)
{
    return $this->builder->where('code', $code);
}
~~~

### Controller
~~~
public function index(TestFilter $filter)
{
    Test::filter($filter)->get();
}
~~~

