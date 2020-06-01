<?php

namespace App\Grid;


use App\Grid\Column\Action;
use App\Grid\Column\ColumnInterface;
use App\Grid\Column\DefaultColumn;

class Configuration
{
    /** @var string[] */
    protected $headers;

    /** @var ColumnInterface[] */
    protected $columns;

    /** @var bool */
    protected $showActions = true;

    /** @var Action[] */
    protected $actions = [];

    /** @var object[] */
    protected $items;

    public function renderField($entity, $field) {
        return $entity->{$field} ?? '';
    }

    /**
     * @return bool
     */
    public function shouldShowActions() {
        return $this->showActions;
    }

    /**
     * @return string[]
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @param string[] $headers
     * @return Configuration
     */
    public function setHeaders(array $headers) {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return ColumnInterface[]
     */
    public function getColumns(): array {
        return $this->columns;
    }

    /**
     * @return ColumnInterface[]
     */
    public function getColumn(string $field): ColumnInterface {
        return $this->columns[$field];
    }

    /**
     * @param ColumnInterface[] $columns
     * @return Configuration
     */
    public function setColumns(array $columns): Configuration {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return Action[]
     */
    public function getActions(): array {
        return $this->actions;
    }

    /**
     * @param Action[] $actions
     * @return Configuration
     */
    public function setActions(array $actions): Configuration {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @param bool $showActions
     * @return Configuration
     */
    public function setShowActions(bool $showActions): Configuration {
        $this->showActions = $showActions;

        return $this;
    }

    /**
     * @return object[]
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * @param object[] $items
     * @return Configuration
     */
    public function setItems($items) {
        $this->items = $items;

        return $this;
    }
}
