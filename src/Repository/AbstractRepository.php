<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

abstract class AbstractRepository extends EntityRepository
{
    /**
     * @return iterable The entities.
     */
    public function findLike(string $term, string $field = 'name'): iterable
    {
        return $this->createQueryBuilder('t')
            ->where(sprintf('t.%s LIKE :pattern', $field))
            ->setParameter('pattern', sprintf('%%%s%%', $term))
            ->getQuery()
            ->execute();
    }

    public function search(string $term): iterable
    {
        return $this->findLike($term);
    }
}
