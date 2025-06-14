<?php

namespace App\DataTable\Column;

use Omines\DataTablesBundle\Column\AbstractColumn;
use Omines\DataTablesBundle\Exception\MissingDependencyException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class FieldWithLink extends AbstractColumn
{
    protected ?\Twig\Environment $twig;

    /**
     * TwigColumn constructor.
     */
    public function __construct(Environment $twig = null) {
        if (!($this->twig = $twig) instanceof \Twig\Environment) {
            throw new MissingDependencyException('You must have TwigBundle installed to use ' . static::class);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function render($value, $context): mixed
    {
        return $this->twig->render(
            $this->getTemplate(),
            [
                'row' => $context,
                'value' => $value,
                'link_route' => $this->options['link_route'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($value): mixed
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): static
    {
        parent::configureOptions($resolver);

        $resolver
            // From TwigColumn
            ->setRequired('template')
            ->setAllowedTypes('template', 'string')
            ->setDefault('template', '_datatables/column/field_with_link.html.twig')

            // Custom
            ->setRequired('link_route')
            ->setAllowedTypes('link_route', ['string']);

        return $this;
    }

    public function getTemplate(): string {
        return $this->options['template'];
    }
}
