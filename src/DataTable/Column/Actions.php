<?php

namespace App\DataTable\Column;

use Omines\DataTablesBundle\Column\TwigColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Actions extends TwigColumn
{
    /**
     * {@inheritdoc}
     */
    protected function render($value, $context) {
        return $this->twig->render(
            $this->getTemplate(),
            [
                'row' => $context,
                'value' => $value,
                'actions' => $this->options['actions']
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($value) {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver
            // From TwigColumn
            ->setRequired('template')
            ->setDefault('template', '_datatables/column/actions.html.twig')
            ->setAllowedTypes('template', 'string')

            // Custom
            ->setDefault('actions', [])
            ->setAllowedTypes('actions', 'array');

        return $this;
    }

    public function getTemplate(): string {
        return $this->options['template'];
    }
}
