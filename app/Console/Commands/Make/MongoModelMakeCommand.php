<?php

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\ModelMakeCommand;

class MongoModelMakeCommand extends ModelMakeCommand
{
    /**
     * The console command name - signature without arguments.
     *
     * @var string
     */
    protected $name = 'make:mongomodel';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'MongoModel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new MongoDb associated Eloquent model class';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();
    }

     /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return resource_path('stubs/MongoModelStub.php');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }
}
