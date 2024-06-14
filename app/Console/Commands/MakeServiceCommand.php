<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $namespace = 'App\Services'; // Base namespace
        $path = app_path('Services');

        // Extract the namespace from the provided name
        $parts = explode('/', $name);
        $className = array_pop($parts); // Get the class name
        $namespace .= '\\' . implode('\\', $parts); // Add the sub-namespace

        if (!str_contains($name, '/')) {
            $namespace = substr($namespace, 0, -1);
        }

        // Build the file path
        $filePath = $path . '/' . implode('/', $parts) . '/' . $className . '.php';

        // Create the directory if it doesn't exist
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Create the service file
        $this->createServiceFile($filePath, $namespace, $className); // Pass $parts

        $filePath = str_replace('//', '\\', $filePath);
        $this->info("Service class {$filePath} created successfully.");

        return Command::SUCCESS;
    }

    /**
     * Create the service file.
     *
     * @param  string  $filePath
     * @param  string  $namespace
     * @param  string  $className
     * @return void
     */
    protected function createServiceFile($filePath, $namespace, $className)
    {
        $stub = file_get_contents(__DIR__ . '/stubs/service.stub');

        // Correctly format the namespace and class name
        $stub = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $className], // Use the first part of the name for the sub-namespace
            $stub
        );

        file_put_contents($filePath, $stub);
    }
}
