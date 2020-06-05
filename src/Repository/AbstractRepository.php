<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

abstract class AbstractRepository extends EntityRepository
{
    /**
     * @param string $term
     * @param string $field
     * @return array The entities.
     */
    public function findLike(string $term, string $field = 'name') {
        return $this->createQueryBuilder('t')
            ->where("t.$field LIKE :pattern")
            ->setParameter('pattern', "%$term%")
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $term
     * @return mixed
     */
    public function search(string $term) {
        return $this->findLike($term);
    }
}
