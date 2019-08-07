<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder;

use Maba\DatabaseInconsistencyFinder\Database\QueryExecutor;
use Maba\DatabaseInconsistencyFinder\Entity\InconsistenciesResult;
use Maba\DatabaseInconsistencyFinder\Entity\ReferencesConfiguration;
use Maba\DatabaseInconsistencyFinder\JobDistribution\JobDistributorFactoryInterface;
use Maba\DatabaseInconsistencyFinder\Service\IntervalManager;

class InconsistencyFinder
{
    private $referencesConfiguration;
    private $queryExecutor;
    private $intervalManager;
    private $jobDistributorFactory;

    public function __construct(
        ReferencesConfiguration $referencesConfiguration,
        QueryExecutor $queryExecutor,
        IntervalManager $intervalManager,
        JobDistributorFactoryInterface $jobDistributorFactory
    ) {
        $this->referencesConfiguration = $referencesConfiguration;
        $this->queryExecutor = $queryExecutor;
        $this->intervalManager = $intervalManager;
        $this->jobDistributorFactory = $jobDistributorFactory;
    }

    public function find(): InconsistenciesResult
    {
        $jobDistributor = $this->jobDistributorFactory->createJobDistributorForIteration();
        $idRange = $this->queryExecutor->getIdRange($this->referencesConfiguration->getReferencedColumn());

        foreach ($this->intervalManager->divideForJobDistribution($idRange) as $interval) {
            $jobDistributor->perform($interval);
        }

        return $jobDistributor->collectResults();
    }
}
