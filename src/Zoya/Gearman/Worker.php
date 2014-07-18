<?php
namespace Zoya\Gearman;

use Psr\Log\LoggerAwareTrait;

/**
 * Class Worker
 * @package Zoya\Gearman
 */
class Worker
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
     * @var \GearmanWorker
     */
    private $worker;

    /**
     * @param string $host
     * @param int $port
     */
    public function __construct($host = 'localhost', $port = 4730)
    {
        $this->host = $host;
        $this->port = $port;
        $this->worker = new \GearmanWorker();
        $this->worker->addServer($this->host, $this->port);

        $this->logger->debug('[' . getmypid() . "] Waiting for job...");

    }

    /**
     * Add new job for worker
     * @param $job
     */
    public function addJob($job)
    {
        $this->worker->addFunction($job, "jobHandler");
    }

    /**
     * Proxy function to handle possible errors
     * @param null $job
     * @return bool
     */
    public function jobHandler($job = null)
    {
        if (empty($job)) {
            $this->logger->warning('No jobs');
            $job->sendFail();
            return false;
        }
        $this->logger->debug("Received job: " . $job->handle());

        $work = json_decode($job->workload());

        if (!empty($work)) {
            (true === processFeed($work)) ?
                $job->sendComplete('OK') :
                $job->sendFail();

        } else {
            $this->logger->error('Empty or corrupted job');
            $job->sendFail();
            return false;
        }
    }

    /**
     * Run worker
     */
    public function run()
    {
        while ($this->worker->work()) {
            if ($this->worker->returnCode() != GEARMAN_SUCCESS) {
                $this->logger->error("return_code: " . $this->worker->returnCode());
                break;
            }
        }

    }
}
