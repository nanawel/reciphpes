<?php

namespace App\Grid\Column;


use App\Document\AbstractDocument;
use Symfony\Component\DependencyInjection\Container;
use Twig\Environment;

class TwigColumn implements ColumnInterface
{
    /** @var Environment */
    protected $twig;

    /** @var string */
    protected $template;

    /** @var string */
    protected $route;

    public function __construct(Environment $twig, string $template, string $route) {
        $this->twig = $twig;
        $this->template = $template;
        $this->route = $route;
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractDocument $document, string $field) {
        return $this->twig->render(
            $this->template,
            [
                'document' => $document,
                'field' => $field,
                'route' => $this->route,
            ]
        );
    }
}
