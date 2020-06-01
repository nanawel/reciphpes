<?php

namespace App\Grid\Column;


use App\Entity\AbstractEntity;
use Symfony\Component\DependencyInjection\Container;

class DefaultColumn implements ColumnInterface
{
    /**
     * @inheritDoc
     */
    public function render(AbstractEntity $entity, string $field) {
        return $entity->{'get' . Container::camelize($field)}();
    }
}
