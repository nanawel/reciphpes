<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;

class RecipeRepository extends AbstractRepository
{
    public function findBy(array $criteria, ?array $sort = null, $limit = null, $skip = null): array {
        // Force default sort order by name
        return parent::findBy($criteria, $sort ?: ['name' => 'ASC'], $limit, $skip);
    }

    /**
     * @inheritDoc
     */
    public function search(string $term) {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('r');
        $qb->leftJoin('r.tags', 't')
            ->where('r.name LIKE :term')->setParameter('term', "%{$term}%")
            ->orWhere('t.name LIKE :term')->setParameter('term', "%{$term}%");

        return $qb
            ->getQuery()
            ->execute();
    }
}
