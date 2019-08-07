<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Entity;

class InconsistenciesResult
{
    /**
     * @var array of IDs
     */
    private $orphanedRecordIds;

    /**
     * @var array, associative. ID => number
     */
    private $missingReferenceCounts;

    /**
     * @var array, associative. ID => number
     */
    private $invalidReferenceCounts;

    public function __construct()
    {
        $this->orphanedRecordIds = [];
        $this->missingReferenceCounts = [];
        $this->invalidReferenceCounts = [];
    }

    public function areInconsistenciesFound(): bool
    {
        return (
            count($this->orphanedRecordIds) > 0
            || count($this->missingReferenceCounts) > 0
            || count($this->invalidReferenceCounts) > 0
        );
    }

    /**
     * @return array
     */
    public function getOrphanedRecordIds(): array
    {
        return $this->orphanedRecordIds;
    }

    /**
     * @param array $orphanedRecordIds
     * @return $this
     */
    public function setOrphanedRecordIds(array $orphanedRecordIds): self
    {
        $this->orphanedRecordIds = $orphanedRecordIds;
        return $this;
    }

    public function addOrphanedRecordId(int $orphanedRecordId): self
    {
        $this->orphanedRecordIds[] = $orphanedRecordId;
        return $this;
    }

    /**
     * @return array
     */
    public function getMissingReferenceCounts(): array
    {
        return $this->missingReferenceCounts;
    }

    /**
     * @param array $missingReferenceCounts
     * @return $this
     */
    public function setMissingReferenceCounts(array $missingReferenceCounts): self
    {
        $this->missingReferenceCounts = $missingReferenceCounts;
        return $this;
    }

    public function addMissingReferenceCount(int $id, int $count): self
    {
        $this->missingReferenceCounts[$id] = $count;
        return $this;
    }

    /**
     * @return array
     */
    public function getInvalidReferenceCounts(): array
    {
        return $this->invalidReferenceCounts;
    }

    /**
     * @param array $invalidReferenceCounts
     * @return $this
     */
    public function setInvalidReferenceCounts(array $invalidReferenceCounts): self
    {
        $this->invalidReferenceCounts = $invalidReferenceCounts;
        return $this;
    }

    public function addInvalidReferenceCount(int $id, int $number): self
    {
        $this->invalidReferenceCounts[$id] = $number;
        return $this;
    }

    public function includeFrom(InconsistenciesResult $result): void
    {
        $this->setMissingReferenceCounts(
            $this->missingReferenceCounts
            + $result->getMissingReferenceCounts()
        );
        $this->setInvalidReferenceCounts(
            $this->invalidReferenceCounts
            + $result->getInvalidReferenceCounts()
        );
        $this->setOrphanedRecordIds(
            array_merge($this->orphanedRecordIds, $result->getOrphanedRecordIds())
        );
    }
}
