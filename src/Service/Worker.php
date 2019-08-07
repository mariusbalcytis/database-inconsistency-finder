<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Service;

use Maba\DatabaseInconsistencyFinder\Entity\InconsistenciesResult;
use Maba\DatabaseInconsistencyFinder\Entity\Interval;

class Worker
{
    private $fastConsistencyValidator;
    private $inconsistencySeeker;
    private $intervalManager;

    public function __construct(
        FastConsistencyValidator $fastConsistencyValidator,
        InconsistencySeeker $inconsistencySeeker,
        IntervalManager $intervalManager
    ) {
        $this->fastConsistencyValidator = $fastConsistencyValidator;
        $this->inconsistencySeeker = $inconsistencySeeker;
        $this->intervalManager = $intervalManager;
    }

    public function validateConsistency(Interval $interval): InconsistenciesResult
    {
        if ($this->fastConsistencyValidator->validateConsistency($interval)) {
            return new InconsistenciesResult();
        }

        $result = new InconsistenciesResult();
        foreach ($this->intervalManager->divideForSeeking($interval) as $interval) {
            $result->includeFrom($this->inconsistencySeeker->seekForInconsistencies($interval));
        }

        return $result;
    }
}
