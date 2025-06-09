<?php

namespace App\Entity;


class Registry
{
    public function __construct(protected array $entityConfigurations)
    {
    }

    /**
     * @param string $entityType
     * @param null|string $config
     * @return mixed|array
     */
    public function getEntityConfig($entityType, $config = null)
    {
        return $config === null
            ? $this->entityConfigurations[$entityType]
            : $this->entityConfigurations[$entityType][$config] ?? null;
    }
}
