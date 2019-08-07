<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\JobDistribution;

use Maba\DatabaseInconsistencyFinder\Entity\InconsistenciesResult;
use Maba\DatabaseInconsistencyFinder\Entity\Interval;
use Maba\DatabaseInconsistencyFinder\Service\Worker;

class SynchronousJobDistributor implements JobDistributorInterface
{
    private $worker;
    private $result;

    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
        $this->result = new InconsistenciesResult();
    }

    public function perform(Interval $interval): void
    {
        $result = $this->worker->validateConsistency($interval);
        if ($result->areInconsistenciesFound()) {
            $this->result->includeFrom($result);
        }
    }

    public function collectResults(): InconsistenciesResult
    {
        return $this->result;
    }
}
