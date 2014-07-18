<?php

namespace Zoya\Gearman;

use Psr\Log\LoggerAwareTrait;

/**
 * Class Client
 * @package Zoya\Gearman
 */
class Client
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    protected $host;
    /**
     * @var int
     */
    protected $port;
    /**
     * @var GearmanClient
     */
    private $client;

    /**
     * @param string $host
     * @param int $port
     */
    public function __construct($host = 'localhost', $port = 4730)
    {
        $this->host = $host;
        $this->port = $port;

        $this->client = new \GearmanClient();
        $this->client->addServer($this->host, $this->port);
        $this->logger->debug("Starting client at " . date("d.m.Y H:i s", time()));
    }

    /**
     * Add new task to worker
     * @param $worker
     * @param $task
     */
    public function addTaskToWorker($worker, $task)
    {
        $this->logger->debug("Add task <$task> to worker <$worker>");
        $this->client->addTaskBackground('feed_worker', json_encode($task));

    }

    /**
     * Run all added tasks
     */
    public function runTasks()
    {
        $this->client->runTasks();
        $this->logger->debug('All tasks added');
        exit(0);
    }
}
