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
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class Kernel extends Application
{
    /** @var ContainerInterface */
    private $container;

    public function boot(): void
    {
        @\umask(0077);

        $configDir = $this->getConfigDirectory();

        // Create container cache if not existing yet
        $cachedContainerFile = $configDir . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'container.php';
        if (false === \file_exists($cachedContainerFile)) {
            $containerBuilder = new ContainerBuilder();
            $this->build($containerBuilder);
            $containerBuilder->compile();
            $this->container = $containerBuilder;

            $dumper = new PhpDumper($containerBuilder);
            \file_put_contents($cachedContainerFile, $dumper->dump());
        }

        // Load cached container
        require_once $cachedContainerFile;
        // ProjectServiceContainer is defined in cache container file
        $this->container = new \ProjectServiceContainer();
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
        foreach (\array_keys($commandServiceDefinitions) as $id) {
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

        $this->makeSureDirectoryExists($configdir);
        $this->makeSureDirectoryExists($configdir . DIRECTORY_SEPARATOR . 'cache');

        \putenv('PET_CONFIG_DIR=' . $configdir);
        return $configdir;
    }

    private function makeSureDirectoryExists(string $configdir): void
    {
        if (false === \is_dir($configdir)) {
            if (false === @\mkdir($configdir, 0700, true)) {
                throw new \RuntimeException('Could not create directory `' . $configdir . '`.');
            }
        }
    }
}
