<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Database;

use Doctrine\DBAL\FetchMode;
use Maba\DatabaseInconsistencyFinder\Entity\Interval;
use Maba\DatabaseInconsistencyFinder\Entity\ReferencedColumn;
use Maba\DatabaseInconsistencyFinder\Entity\TableReferences;

class QueryExecutor
{
    public function calculateHashForInterval(
        ReferencedColumn $referencedColumn,
        Interval $interval
    ): int {
        return (int)$referencedColumn->getConnection()->fetchColumn(
            strtr(
                '
                    SELECT SUM(CRC32(:idColumn) * :countColumn) 
                    FROM :table 
                    WHERE :intervalQuery
                ',
                [
                    ':table' => $referencedColumn->getTableName(),
                    ':idColumn' => $referencedColumn->getIdColumnName(),
                    ':countColumn' => $referencedColumn->getReferenceNumberColumnName(),
                    ':intervalQuery' => $this->prepareIntervalQuery($interval, $referencedColumn->getIdColumnName()),
                ]
            ),
            [
                ':idLargerThan' => $interval->getFrom(),
                ':idUntil' => $interval->getUntil(),
            ]
        );
    }

    public function calculateHashInRelatedTablesForInterval(
        TableReferences $tableReferences,
        Interval $interval
    ): int {
        $connection = $tableReferences->getConnection();
        $total = 0;
        foreach ($tableReferences->getColumnNames() as $columnName) {
            $total += (int)$connection->fetchColumn(
                strtr(
                    '
                    SELECT SUM(CRC32(:idColumn)) 
                    FROM :table 
                    WHERE :intervalQuery
                ',
                    [
                        ':table' => $tableReferences->getTableName(),
                        ':idColumn' => $columnName,
                        ':intervalQuery' => $this->prepareIntervalQuery($interval, $columnName),
                    ]
                ),
                [
                    ':idLargerThan' => $interval->getFrom(),
                    ':idUntil' => $interval->getUntil(),
                ]
            );
        }

        return $total;
    }

    public function findAllReferencedByInterval(ReferencedColumn $referencedColumn, Interval $interval): array
    {
        $statement = $referencedColumn->getConnection()->executeQuery(
            strtr(
                '
                    SELECT :idColumn, :countColumn
                    FROM :table
                    WHERE :intervalQuery
                ',
                [
                    ':table' => $referencedColumn->getTableName(),
                    ':idColumn' => $referencedColumn->getIdColumnName(),
                    ':countColumn' => $referencedColumn->getReferenceNumberColumnName(),
                    ':intervalQuery' => $this->prepareIntervalQuery($interval, $referencedColumn->getIdColumnName()),
                ]
            ),
            [
                ':idLargerThan' => $interval->getFrom(),
                ':idUntil' => $interval->getUntil(),
            ]
        );

        $result = [];
        while (true) {
            $row = $statement->fetch(FetchMode::NUMERIC);
            if ($row === false) {
                break;
            }
            $result[$row[0]] = (int)$row[1];
        }

        return $result;
    }

    /**
     * @param array|TableReferences[] $tableReferencesList
     * @param Interval $interval
     * @return array
     */
    public function aggregateReferencesByInterval(array $tableReferencesList, Interval $interval): array
    {
        $result = [];
        foreach ($tableReferencesList as $tableReferences) {
            $connection = $tableReferences->getConnection();
            foreach ($tableReferences->getColumnNames() as $columnName) {
                $statement = $connection->executeQuery(
                    strtr(
                        '
                            SELECT :idColumn
                            FROM :table
                            WHERE :intervalQuery
                        ',
                        [
                            ':table' => $tableReferences->getTableName(),
                            ':idColumn' => $columnName,
                            ':intervalQuery' => $this->prepareIntervalQuery($interval, $columnName),
                        ]
                    ),
                    [
                        ':idLargerThan' => $interval->getFrom(),
                        ':idUntil' => $interval->getUntil(),
                    ]
                );
                while (true) {
                    $id = $statement->fetch(FetchMode::COLUMN);
                    if ($id === false) {
                        break;
                    }
                    $result[$id]++;
                }
            }
        }

        return $result;
    }

    private function prepareIntervalQuery(Interval $interval, string $idColumn): string
    {
        $parts = [];
        if ($interval->getFrom() !== null) {
            $parts[] = sprintf('%s > :idLargerThan', $idColumn);
        }
        if ($interval->getUntil() !== null) {
            $parts[] = sprintf('%s <= :idUntil', $idColumn);
        }

        return count($parts) > 0 ? implode(' AND ', $parts) : 'TRUE';
    }

    public function getIdRange(ReferencedColumn $referencedColumn): Interval
    {
        $result = $referencedColumn->getConnection()->fetchArray(
            strtr(
                '
                    SELECT MIN(:idColumn), MAX(:idColumn)
                    FROM :table
                ',
                [
                    ':table' => $referencedColumn->getTableName(),
                    ':idColumn' => $referencedColumn->getIdColumnName(),
                ]
            )
        );
        return (new Interval())->setFrom((int)$result[0])->setUntil((int)$result[1]);
    }
}
