<?php


namespace App\Grid;


use App\Grid\Column\Action;
use App\Grid\Column\ColumnInterface;
use Doctrine\ORM\QueryBuilder;

interface Builder
{
    /**
     * @param array $entityConfig
     * @return $this
     */
    public function withEntityConfig(array $entityConfig);

    /**
     * @param QueryBuilder|null $queryBuilder
     * @return $this
     */
    public function withQueryBuilder(?QueryBuilder $queryBuilder);

    /**
     * @param string $query
     * @return $this
     */
    public function withSearchQuery(?string $query);

    /**
     * @param array $criteria
     * @return $this
     */
    public function withSearchCriteria(?array $criteria);

    /**
     * @param string[] $headers
     * @return mixed
     */
    public function withHeaders(array $headers);

    /**
     * @param ColumnInterface[] $columns
     * @return mixed
     */
    public function withColumns(array $columns);

    /**
     * @param Action[] $actions
     * @return mixed
     */
    public function withActions(array $actions);

    /**
     * @return Configuration
     */
    public function build();

    /**
     * @return $this
     */
    public function reset();
}
