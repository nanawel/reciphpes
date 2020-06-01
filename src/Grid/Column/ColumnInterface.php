<?php

namespace App\Grid\Column;


use App\Entity\AbstractEntity;

interface ColumnInterface
{
    /**
     * @param AbstractEntity $entity
     * @param string $field
     * @return string
     */
    public function render(AbstractEntity $entity, string $field);
}
