<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\JobDistribution;

interface JobDistributorFactoryInterface
{
    /**
     * Creates specific job distributor.
     * Job distributor is created for each iteration (whole range division).
     * If two different configurations (tables etc.) would be used, two job distributors would be created
     *
     * @return JobDistributorInterface
     */
    public function createJobDistributorForIteration(): JobDistributorInterface;
}
