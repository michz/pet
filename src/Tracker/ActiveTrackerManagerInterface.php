<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace mztx\pet\Tracker;

interface ActiveTrackerManagerInterface
{
    public function load(): void;

    public function save(): void;

    public function add(): void;

    public function count(): int;

    /** string[][] */
    public function getAll(): array;
}
