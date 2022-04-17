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

use Closure;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\{Arr, ServiceProvider};
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
        Builder::macro('filter', $this->builder());
    }

    protected function builder(): Closure
    {
        return function ($query = null, $params = []): Builder {
            if (empty($query) || ! $query instanceof Builder) {
                if ($query && ! $query instanceof Filter) {
                    $params = Arr::wrap($query);
                }

                $namespace = trim(app('config')->get('filter.namespace'), '\\');

                $arr = explode('\\', get_class($this->model));
                $filter = $namespace . '\\' . end($arr) . 'Filter';

                if (! class_exists($filter)) {
                    throw new InvalidArgumentException("Class {$filter} not found");
                }

                /** @var $query Filter */
                $query = new $filter(request());
            }

            $traits = class_uses_recursive($this->model);
            if (in_array(Filterable::class, $traits)) {
                $query->setIgnoreRequest($this->model::getIgnoreRequest());
                $query->setAcceptRequest($this->model::getAcceptRequest());
            }

            $builder = $query->appendField($params)->filterQuery($this);

            if (in_array(Filterable::class, $traits)) {
                $this->model::ignoreRequest([]);
                $this->model::acceptRequest([]);
            }

            return $builder;
        };
    }
}
