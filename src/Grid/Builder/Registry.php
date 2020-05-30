<?php

namespace App\Grid\Builder;


use App\Grid\Builder;

class Registry
{
    /** @var Builder[] */
    protected $gridBuilders;

    public function __construct(
        iterable $gridBuilders
    ) {
        $this->gridBuilders = iterator_to_array($gridBuilders);
    }

    /**
     * @param string $type
     * @return Builder
     */
    public function getGridBuilder($type) {
        if (!isset($this->gridBuilders[$type])) {
            throw new \InvalidArgumentException('Unknown type: ' . $type);
        }

        return $this->gridBuilders[$type];
    }
}
