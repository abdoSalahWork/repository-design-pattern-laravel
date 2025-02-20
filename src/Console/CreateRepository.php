<?php


namespace Unlimited\Repository\Console;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Pluralizer;
use Unlimited\Repository\Classes\ControllerGenerator;
use Unlimited\Repository\Classes\GeneratorContext;
use Unlimited\Repository\Classes\InterfaceGenerator;
use Unlimited\Repository\Classes\RepositoryGenerator;
use Unlimited\Repository\Console\Traits\DirectoryHelper;
use Unlimited\Repository\Console\Traits\ProviderHelper;

class CreateRepository extends Command
{
    use ProviderHelper;
    use DirectoryHelper;

    protected $signature="repository:create {path}/{name} {--resource}";

    protected $description="Create New Repository";

    public $generatorContext;


    public $files;
    public $name;
    public $originalName;
    public $extraPath;

    public $isExists = false;


    public function handle(Filesystem $filesystem)
    {
        $this->files = $filesystem;

        $this->originalName = $this->argument('name');
        $isResource = $this->option('resource');
        $this->name = $this->getFileName($this->originalName);
        $this->extraPath = $this->getDirctoryPath($this->originalName) . '/path';

        if($this->isExistsFiles($this->name) == true)
        {
            $this->info('Repository files is exists');
            die();
        }

        $this->generateInterface($isResource);
        $this->generateRepository($isResource);
        $this->generateController($isResource);
    }


    public function generateInterface($isResource)
    {
        $this->generatorContext = new GeneratorContext(new InterfaceGenerator($this->name, $this->originalName, $this->extraPath, $this->files));
        $this->generatorContext->generateFile($isResource);
        $this->info('Interface file was created');
    }

    public function generateRepository($isResource)
    {
        $this->generatorContext->setStrategy(new RepositoryGenerator($this->name, $this->originalName, $this->extraPath, $this->files));
        $this->generatorContext->generateFile($isResource);
        $this->info('Repository file was created');
    }

    public function generateController($isResource)
    {
        $this->generatorContext->setStrategy(new ControllerGenerator($this->name, $this->originalName, $this->extraPath, $this->files));
        $this->generatorContext->generateFile($isResource);
        $this->info('Controller file was created');
    }

}
