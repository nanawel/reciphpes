<?php

namespace App\Grid\Column;


use App\Document\AbstractDocument;
use Symfony\Component\DependencyInjection\Container;
use Twig\Environment;
use Twig\Markup;

class TwigColumn implements ColumnInterface
{
    /** @var Environment */
    protected $twig;

    /** @var string */
    protected $template;

    /** @var array */
    protected $options;

    public function __construct(Environment $twig, string $template, array $options = []) {
        $this->twig = $twig;
        $this->template = $template;
        $this->options = $options;
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractDocument $document, string $field) {
        return new Markup(
            $this->twig->render(
                $this->template,
                [
                    'document' => $document,
                    'field' => $field,
                ] + $this->options
            ), 'utf-8'
        );
    }
}
