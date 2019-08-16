<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2019/8/16
 * Time: 18:43
 */
namespace James\Eloquent\Filter;

use Illuminate\Support\ServiceProvider;
use James\Eloquent\Filter\Commands\Filter;

class FilterServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(Filter::class);
    }
}