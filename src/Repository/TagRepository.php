<?php

namespace App\Repository;

class TagRepository extends AbstractRepository
{
    public function findBy(array $criteria, ?array $sort = null, $limit = null, $skip = null): array {
        // Force default sort order by name
        return parent::findBy($criteria, $sort ?: ['name' => 'ASC'], $limit, $skip);
    }
}
