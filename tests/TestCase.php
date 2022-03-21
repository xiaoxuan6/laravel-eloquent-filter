<?php
/**
 * This file is part of PHP CS Fixer.
 *
 * (c) vinhson <15227736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace James\Eloquent\Filter\Tests;

use Illuminate\Foundation\Application;
use James\Eloquent\Filter\FilterServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Factory;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        Factory::guessFactoryNamesUsing(
            function (string $modelName) {
                return 'James\\Eloquent\\Filter\\Tests\\Factory\\' . class_basename($modelName) . 'Factory';
            }
        );
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('filter.namespace', 'James\\Eloquent\\Filter\\Tests\\Filters\\');
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Get package bootstrapper.
     *
     * @param Application $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            FilterServiceProvider::class
        ];
    }
}
