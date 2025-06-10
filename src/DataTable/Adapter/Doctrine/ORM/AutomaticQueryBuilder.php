<?php

declare(strict_types=1);

namespace App\DataTable\Adapter\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\QueryBuilderProcessorInterface;
use Omines\DataTablesBundle\Column\AbstractColumn;
use Omines\DataTablesBundle\DataTableState;

/**
 * AutomaticQueryBuilder.
 *
 * @see \Omines\DataTablesBundle\Adapter\Doctrine\ORM\AutomaticQueryBuilder
 *
 * Using LEFT JOIN for associations in order to retrieve rows even when an association is NULL.
 */
class AutomaticQueryBuilder implements QueryBuilderProcessorInterface
{
    /** @var string */
    private $entityName;

    /** @var string */
    private $entityShortName;

    private array $selectColumns = [];

    private array $joins = [];

    /**
     * AutomaticQueryBuilder constructor.
     */
    public function __construct(private readonly EntityManagerInterface $em, private readonly ClassMetadata $metadata)
    {
        $this->entityName = $this->metadata->getName();
        $this->entityShortName = mb_strtolower($this->metadata->getReflectionClass()->getShortName());
    }

    /**
     * {@inheritdoc}
     */
    public function process(QueryBuilder $builder, DataTableState $state): void
    {
        if ($this->selectColumns === [] && $this->joins === []) {
            foreach ($state->getDataTable()->getColumns() as $column) {
                $this->processColumn($column);
            }
        }

        $builder->from($this->entityName, $this->entityShortName);
        $this->setSelectFrom($builder);
        $this->setJoins($builder);
    }

    protected function processColumn(AbstractColumn $column) {
        $field = $column->getField();

        // Default to the column name if that corresponds to a field mapping
        if (! isset($field) && isset($this->metadata->fieldMappings[$column->getName()])) {
            $field = $column->getName();
        }

        if (null !== $field) {
            $this->addSelectColumns($field);
        }
    }

    private function addSelectColumns(string $field): void
    {
        $currentPart = $this->entityShortName;
        $currentAlias = $currentPart;
        $metadata = $this->metadata;

        $parts = explode('.', $field);

        if (count($parts) > 1 && $parts[0] === $currentPart) {
            array_shift($parts);
        }

        if (count($parts) > 1 && $field = $metadata->hasField(implode('.', $parts))) {
            $this->addSelectColumn($currentAlias, implode('.', $parts));
        } else {
            while (count($parts) > 1) {
                $previousPart = $currentPart;
                $previousAlias = $currentAlias;
                $currentPart = array_shift($parts);
                $currentAlias = ($previousPart === $this->entityShortName ? '' : $previousPart . '_') . $currentPart;

                $this->joins[$previousAlias . '.' . $currentPart] = ['alias' => $currentAlias, 'type' => 'leftJoin'];

                $metadata = $this->setIdentifierFromAssociation($currentAlias, $currentPart, $metadata);
            }

            $this->addSelectColumn($currentAlias, $this->getIdentifier($metadata));
            $this->addSelectColumn($currentAlias, $parts[0]);
        }
    }

    private function addSelectColumn($columnTableName, ?string $data): static
    {
        if (isset($this->selectColumns[$columnTableName])) {
            if (!in_array($data, $this->selectColumns[$columnTableName], true)) {
                $this->selectColumns[$columnTableName][] = $data;
            }
        } else {
            $this->selectColumns[$columnTableName][] = $data;
        }

        return $this;
    }

    private function getIdentifier(ClassMetadata $metadata): ?string
    {
        $identifiers = $metadata->getIdentifierFieldNames();

        return array_shift($identifiers);
    }

    private function setIdentifierFromAssociation(string $association, string $key, ClassMetadata $metadata) {
        $targetEntityClass = $metadata->getAssociationTargetClass($key);

        /** @var ClassMetadata $targetMetadata */
        $targetMetadata = $this->em->getMetadataFactory()->getMetadataFor($targetEntityClass);
        $this->addSelectColumn($association, $this->getIdentifier($targetMetadata));

        return $targetMetadata;
    }

    private function setSelectFrom(QueryBuilder $qb): static
    {
        foreach ($this->selectColumns as $key => $value) {
            if (false === ($key === 0 || ($key === '' || $key === '0'))) {
                $qb->addSelect('partial ' . $key . '.{' . implode(',', $value) . '}');
            } else {
                $qb->addSelect($value);
            }
        }

        return $this;
    }

    private function setJoins(QueryBuilder $qb): static
    {
        foreach ($this->joins as $key => $value) {
            $qb->{$value['type']}($key, $value['alias']);
        }

        return $this;
    }
}
