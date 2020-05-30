<?php

namespace App\Grid\Column;


use App\Document\AbstractDocument;

interface ColumnInterface
{
    /**
     * @param AbstractDocument $document
     * @param string $field
     * @return string
     */
    public function render(AbstractDocument $document, string $field);
}
