<?php

namespace Pratiksh\Payable\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;

class MakePaymentGatewayCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payable:gateway {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new payable payment gateway.';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../Console/Commands/Stubs/PaymentGateway.stub';
    }

    public function handle()
    {
        $name = $this->argument('name');
        $className = class_basename($name);
        $exploded_name = explode('\\', $name);
        $namespace = $this->laravel->getNamespace().'PaymentGateway'.(count($exploded_name) > 1 ? '\\' : '').trim(implode('\\', array_slice($exploded_name, 0, -1)), '\\');

        $directory = app_path('PaymentGateway/'.(str_replace('\\', '/', trim(implode('\\', array_slice($exploded_name, 0, -1)), '\\'))));
        $fileName = $className.'.php';
        $filePath = $directory.'/'.$fileName;

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($filePath)) {
            $this->error("Class '{$className}' already exists in namespace '{$namespace}'");

            return;
        }

        $stub = File::get($this->getStub());
        $stub = str_replace('{{NAMESPACE}}', "{$namespace}", $stub);
        $stub = str_replace('{{CLASS_NAME}}', $className, $stub);

        File::put($filePath, $stub);

        $this->info("Payment gateway '{$className}' created successfully at '{$filePath}'");
    }
}
