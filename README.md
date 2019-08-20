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
搜索默认数据库字段
    public function code($code)
    {
        return $this->builder->where('code', $code);
    }
    
    public function name()
    {
        $this->builder->where('name', 'like', "%测试%");
    }
    
    public function mobile($mobile)
    {
        $mobile ? $this->builder->where('mobile', $mobile) : $this->builder;
    }

 一对多
    public function title($title)
    {
        return $this->builder->whereHas('goods', function($q) use ($title){
            return $q->where('title', 'like', "%{$title}%");
        });
    }
~~~

### Controller
~~~
// http://baby.com/api/city?code=54526481&title=菁华粉底液（片装1.5mll）

public function index(TestFilter $filter)
{
    Test::filter($filter)->get(); 
    
    // 支持传参
    Test::filter($filter, 'name')->get();
    // Or
    Test::filter($filter, ['name'])->get();
    
    // 支持传参带参数
    $mobile = "18551233215";
    Test::filter($filter, ['mobile:'.$mobile])->get();
}

~~~

