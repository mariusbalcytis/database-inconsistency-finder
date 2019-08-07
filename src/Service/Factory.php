<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Service;

use Maba\DatabaseInconsistencyFinder\Database\QueryExecutor;
use Maba\DatabaseInconsistencyFinder\Entity\ReferencesConfiguration;
use Maba\DatabaseInconsistencyFinder\InconsistencyFinder;
use Maba\DatabaseInconsistencyFinder\JobDistribution\SynchronousJobDistributorFactory;

class Factory
{
    private $sizeForSeeking;
    private $sizeForJobDistribution;

    public function __construct(int $sizeForSeeking = 1000, int $sizeForJobDistribution = 10000)
    {
        $this->sizeForSeeking = $sizeForSeeking;
        $this->sizeForJobDistribution = $sizeForJobDistribution;
    }

    public function createInconsistencyFinder(ReferencesConfiguration $referencesConfiguration)
    {
        $queryExecutor = new QueryExecutor();
        $intervalManager = new IntervalManager($this->sizeForSeeking, $this->sizeForJobDistribution);
        return new InconsistencyFinder(
            $referencesConfiguration,
            $queryExecutor,
            $intervalManager,
            new SynchronousJobDistributorFactory(
                new Worker(
                    new FastConsistencyValidator(
                        $referencesConfiguration,
                        $queryExecutor
                    ),
                    new InconsistencySeeker(
                        $referencesConfiguration,
                        $queryExecutor
                    ),
                    $intervalManager
                )
            )
        );
    }
}
