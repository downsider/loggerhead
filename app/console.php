<?php

$appDir = dirname(__DIR__);

include_once($appDir . "/vendor/autoload.php");

use Silktide\Syringe\ReferenceResolver;
use Silktide\Syringe\ContainerBuilder;
use Silktide\Syringe\Loader\JsonLoader;
use Silktide\Syringe\Loader\YamlLoader;
use Downsider\Loggerhead\PuzzleConfig;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

ini_set("display_errors", 1);
error_reporting(E_ALL);

$resolver = new ReferenceResolver();
$loaders = [
    new JsonLoader(),
    new YamlLoader()
];

$configPaths = [
    $appDir . "/app/config",
    $appDir
];

$builder = new ContainerBuilder($resolver, $configPaths);

foreach ($loaders as $loader) {
    $builder->addLoader($loader);
}

$builder->setApplicationRootDirectory($appDir);

$puzzleConfigs = PuzzleConfig::getConfigPaths("silktide/syringe");
$builder->addConfigFiles($puzzleConfigs);
$builder->addConfigFile("services.yml");

$container = $builder->createContainer();

// find all commands and add them to the application
$serviceNames = $container->keys();
$application = new Application("CLI", "1.0.0");
foreach ($serviceNames as $name) {
    // commands are suffixed with ".command" ...
    if (strrpos($name, ".command") == strlen($name) - 8) {
        $command = $container[$name];
        // ... and are instances of the Symfony Command class
        if ($command instanceof Command) {
            $application->add($command);
        }
    }
}

// Run the app
$application->run();