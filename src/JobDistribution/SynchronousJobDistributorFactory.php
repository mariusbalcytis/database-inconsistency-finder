<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\JobDistribution;

use Maba\DatabaseInconsistencyFinder\Service\Worker;

class SynchronousJobDistributorFactory implements JobDistributorFactoryInterface
{
    private $worker;

    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
    }

    public function createJobDistributorForIteration(): JobDistributorInterface
    {
        return new SynchronousJobDistributor($this->worker);
    }
}
