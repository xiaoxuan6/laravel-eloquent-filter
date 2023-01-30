## Installation

This package can be installed through Composer.

```
composer require james.xue/laravel-eloquent-filter
```

## 使用
~~~
php artisan make:filter Test
~~~

## 修改生成文件命名空间
```angular2html
默认命名空间：App\Filters

先执行 php artisan vendor:publish --tag=filter
然后修改 config 中的 namespace

```

### TestFilter 

~~~
搜索默认数据库字段
    public function code($code)
    {
        return $this->builder->where('code', $code);
    }
    
    public function name()
    {
        return $this->builder->where('name', 'like', "%测试%");
    }
    
    public function mobile($mobile)
    {
        return $mobile ? $this->builder->where('mobile', $mobile) : $this->builder;
    }

 一对多
    public function title($title)
    {
        return $this->builder->whereHas('goods', function($q) use ($title){
            return $q->where('title', 'like', "%{$title}%");
        });
    }
~~~

### Controller、通过 url 筛选条件默认方法可以不写，默认搜索条件为 where($column, $keywords)
~~~
// http://baby.com/api/city?code=54526481&title=菁华粉底液（片装1.5mll）

public function index(TestFilter $testfilter)
{
    Test::filter($testfilter)->get(); 
    // Or $testfilter 可以省略，但是 TestFilter 文件必须存在，默认加载 `模型 + Filter`
    Test::filter()->get(); 
    
    // 支持固定筛选
    Test::filter($testfilter, 'name')->get();
    // Or
    Test::filter($testfilter, ['name'])->get();
    
    // 支持固定筛选带参数
    $mobile = "185513215";
    Test::filter($testfilter, ['mobile:'.$mobile])->get();
    
    // 支持自定义筛选方式，example：<>、like等
    $mobile = "185513215";
    Test::filter($testfilter, ['mobile:'.$mobile."|<>"])->get();
}

~~~

