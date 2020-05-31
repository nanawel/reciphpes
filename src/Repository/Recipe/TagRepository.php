<?php

namespace App\Repository\Recipe;

use App\Repository\AbstractRepository;

class TagRepository extends AbstractRepository
{
    public function findBy(array $criteria, ?array $sort = null, $limit = null, $skip = null): array {
        // Force default sort order by name
        return parent::findBy($criteria, $sort ?: ['name' => 'ASC'], $limit, $skip);
    }

    public function findLike($term) {
        return $this->findBy(['_id' => ['$regex' => $term, '$options' => 'i']]);
    }
}
