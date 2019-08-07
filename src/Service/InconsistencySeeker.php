<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Service;

use Maba\DatabaseInconsistencyFinder\Database\QueryExecutor;
use Maba\DatabaseInconsistencyFinder\Entity\InconsistenciesResult;
use Maba\DatabaseInconsistencyFinder\Entity\Interval;
use Maba\DatabaseInconsistencyFinder\Entity\ReferencesConfiguration;

class InconsistencySeeker
{
    private $referencesConfiguration;
    private $queryExecutor;

    public function __construct(ReferencesConfiguration $referencesConfiguration, QueryExecutor $queryExecutor)
    {
        $this->referencesConfiguration = $referencesConfiguration;
        $this->queryExecutor = $queryExecutor;
    }

    public function seekForInconsistencies(Interval $interval): InconsistenciesResult
    {
        $result = new InconsistenciesResult();

        $expected = $this->queryExecutor->findAllReferencedByInterval(
            $this->referencesConfiguration->getReferencedColumn(),
            $interval
        );

        $actual = $this->queryExecutor->aggregateReferencesByInterval(
            $this->referencesConfiguration->getTableReferencesList(),
            $interval
        );

        foreach ($expected as $id => $number) {
            if (!isset($actual[$id])) {
                $result->addOrphanedRecordId($id);
            } elseif ($actual[$id] !== $expected[$id]) {
                $result->addInvalidReferenceCount($id, $actual[$id]);
            }

            unset($actual[$id]);
        }

        $result->setMissingReferenceCounts($actual);

        return $result;
    }
}
