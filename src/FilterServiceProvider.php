<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2019/8/16
 * Time: 18:43
 */

namespace James\Eloquent\Filter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use James\Eloquent\Filter\Commands\FilterMakeCommand;

class FilterServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(FilterMakeCommand::class);
        $this->mergeConfigFrom(__DIR__ . '/../config/filter.php', 'filter');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/filter.php' => config_path('filter.php')
            ], 'filter');
        }

        $this->registerFilter();

    }

    /**
     * Notes: set Builder filter
     * Date: 2020/6/11 18:31
     */
    protected function registerFilter()
    {
        Builder::macro('filter', function ($query = null, $params = []): Builder {

            if (empty($query)) {

                $namespace = trim(app('config')->get('filter.namespace'), '\\');

                $flter = $namespace . "\\" . basename(get_class($this->model)) . "Filter";

                if (!class_exists($flter))
                    throw new \InvalidArgumentException("Class {$flter} not found");

                $query = new $flter(request());

            }

            return $query->appendField($params)->filterQuery($this);

        });
    }
}