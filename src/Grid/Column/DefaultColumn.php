<?php

namespace App\Grid\Column;


use App\Document\AbstractDocument;
use Symfony\Component\DependencyInjection\Container;

class DefaultColumn implements ColumnInterface
{
    /**
     * @inheritDoc
     */
    public function render(AbstractDocument $document, string $field) {
        return $document->{'get' . Container::camelize($field)}();
    }
}
