<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\JobDistribution;

use Maba\DatabaseInconsistencyFinder\Entity\InconsistenciesResult;
use Maba\DatabaseInconsistencyFinder\Entity\Interval;

interface JobDistributorInterface
{
    /**
     * Performs actual job.
     * Results should be saved somewhere to be later on retrieved (from all jobs) by `collectResults` method.
     *
     * @param Interval $interval
     */
    public function perform(Interval $interval): void;

    /**
     * Collects and merges all results from all calls to `perform` method.
     * Should hang and wait if not all jobs are completed yet.
     *
     * @return InconsistenciesResult
     */
    public function collectResults(): InconsistenciesResult;
}
