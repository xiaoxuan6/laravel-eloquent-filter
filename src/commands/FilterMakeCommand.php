<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2020/6/11
 * Time: 14:40
 */

namespace James\Eloquent\Filter\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class FilterMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent Model Filter';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/filter.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return trim(config('filter')['namespace'], '\\');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $name = trim($this->argument('name'));

        if (!Str::endsWith($name, 'Filter')) {
            $name .= 'Filter';
        }

        $this->type = ucfirst($name);

        return $this->type;
    }
}