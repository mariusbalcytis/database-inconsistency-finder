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

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param Connection $connection
     * @return $this
     */
    public function setConnection(Connection $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     * @return $this
     */
    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdColumnName(): string
    {
        return $this->idColumnName;
    }

    /**
     * @param string $idColumnName
     * @return $this
     */
    public function setIdColumnName(string $idColumnName): self
    {
        $this->idColumnName = $idColumnName;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceNumberColumnName(): string
    {
        return $this->referenceNumberColumnName;
    }

    /**
     * @param string $referenceNumberColumnName
     * @return $this
     */
    public function setReferenceNumberColumnName(string $referenceNumberColumnName): self
    {
        $this->referenceNumberColumnName = $referenceNumberColumnName;
        return $this;
    }
}
