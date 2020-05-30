<?php

namespace App\Grid\Column;


use App\Document\AbstractDocument;
use Twig\Environment;

class Action implements ColumnInterface
{
    /** @var Environment */
    protected $twig;

    /** @var string */
    protected $label;

    /** @var string */
    protected $route;

    /** @var string */
    protected $class;

    /** @var int */
    protected $sortOrder = 0;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractDocument $document, string $field) {
        return $this->twig->render(
            '_grid/action.html.twig',
            [
                'action' => $this,
                'document' => $document
            ]
        );
    }

    /**
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Action
     */
    public function setLabel(string $label): Action {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoute(): string {
        return $this->route;
    }

    /**
     * @param string $route
     * @return Action
     */
    public function setRoute(string $route): Action {
        $this->route = $route;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string {
        return $this->class;
    }

    /**
     * @param string $class
     * @return Action
     */
    public function setClass(string $class): Action {
        $this->class = $class;
        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder(): int {
        return $this->sortOrder;
    }

    /**
     * @param int $sortOrder
     * @return Action
     */
    public function setSortOrder(int $sortOrder): Action {
        $this->sortOrder = $sortOrder;
        return $this;
    }
}
