<?php

namespace App\Grid\Column;


use App\Entity\AbstractEntity;
use Twig\Environment;
use Twig\Markup;

class TwigColumn implements ColumnInterface
{
    /** @var Environment */
    protected $twig;

    /** @var string */
    protected $template;

    /** @var array */
    protected $context;

    public function __construct(Environment $twig, ?string $template = null, array $context = []) {
        $this->twig = $twig;
        $this->template = $template;
        $this->context = $context;
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractEntity $entity, string $field) {
        if (! $this->template) {
            throw new \Exception('A template must be provided for the column.');
        }
        return new Markup(
            $this->twig->render(
                $this->template,
                [
                    'entity' => $entity,
                    'field' => $field,
                ] + $this->context
            ), 'utf-8'
        );
    }
}
