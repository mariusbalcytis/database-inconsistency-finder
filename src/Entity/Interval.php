<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Entity;

class Interval
{
    /**
     * @var int|null
     */
    private $from;

    /**
     * @var int|null
     */
    private $until;

    public function getFrom(): ?int
    {
        return $this->from;
    }

    public function setFrom(?int $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function getUntil(): ?int
    {
        return $this->until;
    }

    public function setUntil(?int $until): self
    {
        $this->until = $until;
        return $this;
    }
}
