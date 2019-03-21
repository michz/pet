<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace mztx\pet\Command;

use mztx\pet\Tracker\ActiveTrackerManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TrackerStartCommand extends Command
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ActiveTrackerManagerInterface */
    private $activeTrackerManager;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ActiveTrackerManagerInterface $activeTrackerManager
    ) {
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
        $this->activeTrackerManager = $activeTrackerManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tracker:start')
            ->setDescription('Starts a tracker.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->activeTrackerManager->add();
        $this->activeTrackerManager->save();
        $output->writeln('<info>Tracker started.</info>');
        return 0;
    }
}
