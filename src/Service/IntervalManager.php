<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Service;

use Maba\DatabaseInconsistencyFinder\Entity\Interval;
use Generator;

class IntervalManager
{
    private $sizeForSeeking;
    private $sizeForJobDistribution;

    public function __construct(int $sizeForSeeking, int $sizeForJobDistribution)
    {
        $this->sizeForSeeking = $sizeForSeeking;
        $this->sizeForJobDistribution = $sizeForJobDistribution;
    }

    /**
     * Divides interval into parts for job distribution.
     * Always includes first and last part without from/until restrictions to handle new added IDs
     *
     * @param Interval $idRange
     * @return Generator|Interval[]
     */
    public function divideForJobDistribution(Interval $idRange): Generator
    {
        $from = $idRange->getFrom() + $this->sizeForSeeking;
        $max = $idRange->getUntil() - $this->sizeForSeeking;

        if ($from >= $max) {
            yield (new Interval());
            return;
        }

        yield (new Interval())->setUntil($from);

        while ($from < $max) {
            $until = min($from + $this->sizeForJobDistribution, $max);
            yield (new Interval())->setFrom($from)->setUntil($until);
            $from = $until;
        }

        yield (new Interval())->setFrom($max);
    }

    /**
     * Divides interval into smaller ones which could be used for in-memory seeking.
     * If interval is already small enough, null is returned
     *
     * @param Interval $interval
     * @return Interval[]|Generator
     */
    public function divideForSeeking(Interval $interval): Generator
    {
        if ($interval->getUntil() === null || $interval->getFrom() === null) {
            yield $interval;
            return;
        }

        $from = $interval->getFrom();
        while ($from < $interval->getUntil()) {
            $until = min($from + $this->sizeForSeeking, $interval->getUntil());
            yield (new Interval())->setFrom($from)->setUntil($until);
            $from = $until;
        }
    }
}
