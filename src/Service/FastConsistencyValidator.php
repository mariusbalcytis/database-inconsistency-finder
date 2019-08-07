<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Service;

use Maba\DatabaseInconsistencyFinder\Database\QueryExecutor;
use Maba\DatabaseInconsistencyFinder\Entity\Interval;
use Maba\DatabaseInconsistencyFinder\Entity\ReferencesConfiguration;

class FastConsistencyValidator
{
    private $referencesConfiguration;
    private $queryExecutor;

    public function __construct(ReferencesConfiguration $referencesConfiguration, QueryExecutor $queryExecutor)
    {
        $this->referencesConfiguration = $referencesConfiguration;
        $this->queryExecutor = $queryExecutor;
    }

    public function validateConsistency(Interval $interval): bool
    {
        $expectedSum = $this->queryExecutor->calculateHashForInterval(
            $this->referencesConfiguration->getReferencedColumn(),
            $interval
        );
        $actualSum = 0;
        foreach ($this->referencesConfiguration->getTableReferencesList() as $tableReferences) {
            $actualSum += $this->queryExecutor->calculateHashInRelatedTablesForInterval(
                $tableReferences,
                $interval
            );
        }

        return $expectedSum === $actualSum;
    }
}
