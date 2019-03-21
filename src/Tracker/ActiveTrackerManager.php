<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace mztx\pet\Tracker;

use ParseCsv\Csv;

class ActiveTrackerManager implements ActiveTrackerManagerInterface
{
    private const ACTIVE_TRACKER_FILENAME = 'active.csv';

    /** @var string[][] */
    private $trackers = [];

    /** @var string */
    private $filename;

    /** @var string */
    private $dateTimeFormat;

    public function __construct(
        string $configDir,
        string $dateTimeFormat
    ) {
        $this->filename = $configDir . DIRECTORY_SEPARATOR . self::ACTIVE_TRACKER_FILENAME;
        $this->dateTimeFormat = $dateTimeFormat;

        $this->load();
    }

    public function load(): void
    {
        var_dump($this->filename);
        $csv = new Csv();
        $csv->heading = false;
        $csv->linefeed = PHP_EOL;
        $csv->parse($this->filename);
        $this->trackers = $csv->data;
        var_dump($this->trackers);
    }

    public function save(): void
    {
        $csv = new Csv();
        $csv->linefeed = PHP_EOL;
        $csv->heading = false;
        $csv->data = $this->trackers;
        $csv->save($this->filename);
    }

    public function add(): void
    {
        $this->trackers[] = [
            0 => (new \DateTime())->format($this->dateTimeFormat),
            1 => '',
        ];
    }

    public function count(): int
    {
        return \count($this->trackers);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): array
    {
        return $this->trackers;
    }
}
