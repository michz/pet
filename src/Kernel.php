<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace mztx\pet;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class Kernel extends Application
{
    /** @var ContainerInterface */
    private $container;

    public function boot(): void
    {
        // @TODO Load Container cached if existing

        $configDir = $this->getConfigDirectory();
        $containerBuilder = new ContainerBuilder();
        $this->build($containerBuilder);

        $containerBuilder->compile(true);
        $this->container = $containerBuilder;
    }

    public function build(ContainerBuilder $containerBuilder): void
    {
        $loader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config')
        );

        $loader->load('services.yaml');

        $applicationDefinition = $containerBuilder->getDefinition(Application::class);
        $commandServiceDefinitions = $containerBuilder->findTaggedServiceIds('pet.cli.command');
        foreach ($commandServiceDefinitions as $id => $commandServiceDefinition) {
            $applicationDefinition->addMethodCall('add', [new Reference($id)]);
        }
    }

    public function getApplication(): ConsoleApplication
    {
        return $this->container->get(Application::class);
    }

    private function getConfigDirectory(): string
    {
        $configdir = null;
        if (isset($_ENV['PET_CONFIG_DIR'])) {
            $configdir = $_ENV['PET_CONFIG_DIR'];
        } elseif (isset($_SERVER['HOME'])) {
            $configdir = $_SERVER['HOME'] . DIRECTORY_SEPARATOR . '.pet';
        } elseif (isset($_SERVER['HOMEDRIVE']) && isset($_SERVER['HOMEPATH'])) {
            $configdir = \rtrim($_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.pet';
        } else {
            throw new \RuntimeException('Could not determine config dir. Please provide one explicitly by settings ENV variable PET_CONFIG_DIR.');
        }

        if (false === \is_dir($configdir)) {
            if (false === @\mkdir($configdir, 0700, true)) {
                throw new \RuntimeException('Could not create config directory.');
            }
        }

        return $configdir;
    }
}
