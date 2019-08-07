<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Entity;

use Doctrine\DBAL\Connection;

class ReferencedColumn
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $idColumnName;

    /**
     * @var string
     */
    private $referenceNumberColumnName;

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function setConnection(Connection $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function getIdColumnName(): string
    {
        return $this->idColumnName;
    }

    public function setIdColumnName(string $idColumnName): self
    {
        $this->idColumnName = $idColumnName;
        return $this;
    }

    public function getReferenceNumberColumnName(): string
    {
        return $this->referenceNumberColumnName;
    }

    public function setReferenceNumberColumnName(string $referenceNumberColumnName): self
    {
        $this->referenceNumberColumnName = $referenceNumberColumnName;
        return $this;
    }
}
