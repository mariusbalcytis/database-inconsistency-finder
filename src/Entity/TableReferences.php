<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Entity;

use Doctrine\DBAL\Connection;

class TableReferences
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
     * @var string[]
     */
    private $columnNames;

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
     * @return string[]
     */
    public function getColumnNames(): array
    {
        return $this->columnNames;
    }

    /**
     * @param string[] $columnNames
     * @return $this
     */
    public function setColumnNames(array $columnNames): self
    {
        $this->columnNames = $columnNames;
        return $this;
    }
}
