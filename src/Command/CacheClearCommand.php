<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace mztx\pet\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends Command
{
    /** @var string */
    private $configDir;

    public function __construct(
        string $configDir
    ) {
        parent::__construct();
        $this->configDir = $configDir;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Clears the cache.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \unlink($this->configDir . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'container.php');
        $output->writeln('<info>Cache cleared.</info>');
        return 0;
    }
}
