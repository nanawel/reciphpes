<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

abstract class AbstractRepository extends EntityRepository
{
    public function findLike($term, $field = 'name') {
        return $this->createQueryBuilder('t')
            ->where("t.$field LIKE :pattern")
            ->setParameter('pattern', "%$term%")
            ->getQuery()
            ->execute();
    }
}
