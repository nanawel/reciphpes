<?php

namespace App\Repository;


use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

abstract class AbstractRepository extends DocumentRepository
{
    public function findAllByIds(array $ids) {
        $qb = $this->createQueryBuilder()->field('id')->in($ids)
            ->getQuery();

        return $qb->execute();
    }
}
