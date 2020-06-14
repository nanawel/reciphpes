<?php

namespace App\Grid\Column;


use App\Entity\AbstractEntity;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class Action implements ColumnInterface
{
    /** @var Environment */
    protected $twig;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var string */
    protected $label;

    /** @var string */
    protected $route;

    /** @var string */
    protected $class;

    /** @var int */
    protected $sortOrder = 0;

    public function __construct(Environment $twig, TranslatorInterface $translator) {
        $this->twig = $twig;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractEntity $entity, string $field) {
        return $this->twig->render(
            '_grid/action.html.twig',
            [
                'action' => $this,
                'entity' => $entity,
            ]
        );
    }

    /**
     * @return string
     */
    public function getLabel(): string {
        return $this->translator->trans($this->label);
    }

    /**
     * @param string $label
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setSortOrder(int $sortOrder): Action {
        $this->sortOrder = $sortOrder;

        return $this;
    }
}
