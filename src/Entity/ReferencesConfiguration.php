<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Entity;

class ReferencesConfiguration
{
    /**
     * @var ReferencedColumn
     */
    private $referencedColumn;

    /**
     * @var TableReferences[]
     */
    private $tableReferencesList;

    public function __construct()
    {
        $this->tableReferencesList = [];
    }

    /**
     * @return ReferencedColumn
     */
    public function getReferencedColumn(): ReferencedColumn
    {
        return $this->referencedColumn;
    }

    /**
     * @param ReferencedColumn $referencedColumn
     * @return $this
     */
    public function setReferencedColumn(ReferencedColumn $referencedColumn): self
    {
        $this->referencedColumn = $referencedColumn;
        return $this;
    }

    /**
     * @return TableReferences[]
     */
    public function getTableReferencesList(): array
    {
        return $this->tableReferencesList;
    }

    /**
     * @param TableReferences[] $tableReferencesList
     * @return $this
     */
    public function setTableReferencesList(array $tableReferencesList): self
    {
        $this->tableReferencesList = $tableReferencesList;
        return $this;
    }

    public function addTableReferences(TableReferences $tableReferences): self
    {
        $this->tableReferencesList[] = $tableReferences;
        return $this;
    }
}
