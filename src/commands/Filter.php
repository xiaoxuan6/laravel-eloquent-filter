<?php

namespace James\Eloquent\Filter\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Filter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filter {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create A New Eloquent Model Filter';

    /**
     * Class to create.
     *
     * @var array|string
     */
    protected $class;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->makeClassName()->putFile();
        $this->info(class_basename($this->getClassName()) . ' Created Successfully!');
    }

    /**
     * Notes: create file
     * Date: 2019/8/16 18:30
     */
    public function putFile()
    {
        if ($this->files->exists($path = $this->getPath())) {
            $this->error($path . ' Already Exists!');
            die;
        }

        $this->makeDirectory($path);

        $string = $this->string($this->getClassName());

        $this->files->put($path, $string);
    }

    /**
     * Notes: get file path
     * @return string
     */
    public function getPath()
    {
        return $this->laravel->path . DIRECTORY_SEPARATOR . 'Filter' . DIRECTORY_SEPARATOR . $this->getClassName() . '.php';
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Create Filter Class Name.
     *
     * @return $this
     */
    public function makeClassName()
    {
        $parts = array_map('studly_case', explode('\\', $this->argument('name')));
        $className = last($parts);

        if (!Str::endsWith($className, 'Filter')) {
            $className .= 'Filter';
        }

        if (class_exists("App\\Filter\\" . $className)) {
            $this->error("$className Already Exists!");
            die;
        }

        $this->setClassName($className);

        return $this;
    }

    /**
     * set ClassName
     * @return $this
     */
    public function setClassName($name)
    {
        $this->class = $name;

        return $this;
    }

    /**
     * get ClassName
     * @return array|string
     */
    public function getClassName()
    {
        return $this->class;
    }

    public function string($class)
    {
        return "<?php 
namespace App\Filter;

use James\Eloquent\Filter\Filter;

class $class extends Filter
{
    /**
     * Array of input used to filter the query.
     *
     * @var array
     */
    protected $filterField = [];

}";
    }
}
