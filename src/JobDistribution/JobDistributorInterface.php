<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\JobDistribution;

use Maba\DatabaseInconsistencyFinder\Entity\InconsistenciesResult;
use Maba\DatabaseInconsistencyFinder\Entity\Interval;

interface JobDistributorInterface
{
    public function perform(Interval $interval);

    public function collectResults(): InconsistenciesResult;
}
