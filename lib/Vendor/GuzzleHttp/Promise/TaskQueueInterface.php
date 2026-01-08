<?php

declare (strict_types=1);
namespace OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Promise;

/** @internal */
interface TaskQueueInterface
{
    /**
     * Returns true if the queue is empty.
     */
    public function isEmpty() : bool;
    /**
     * Adds a task to the queue that will be executed the next time run is
     * called.
     */
    public function add(callable $task) : void;
    /**
     * Execute all of the pending task in the queue.
     */
    public function run() : void;
}
