<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\JobDistribution;

interface JobDistributorFactoryInterface
{
    public function createJobDistributorForIteration(): JobDistributorInterface;
}
