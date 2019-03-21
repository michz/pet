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

class TrackerListCommand extends Command
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
            ->setName('tracker:list')
            ->setDescription('Lists all active trackers.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $trackers = $this->activeTrackerManager->getAll();

        foreach ($trackers as $tracker) {
            $output->writeln(\implode(',', $tracker));
        }

        return 0;
    }
}
