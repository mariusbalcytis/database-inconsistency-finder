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

    /**
     * @return int|null
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param int|null $from
     * @return $this
     */
    public function setFrom($from): self
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * @param int|null $until
     * @return $this
     */
    public function setUntil($until): self
    {
        $this->until = $until;
        return $this;
    }
}
