<?php

namespace App\Grid\Column;

use App\Entity\AbstractEntity;

class FieldWithLinkColumn extends TwigColumn
{
    public function render(AbstractEntity $entity, string $field) {
        if (! $this->template) {
            $this->template = '_grid/field_with_link.html.twig';
        }
        if (! isset($this->context['link_route'])) {
            if (! isset($this->context['link_route_callback'])) {
                throw new \Exception('Missing context keys "link_route" or "link_route_callback".');
            }
            $this->context['link_route'] = $this->context['link_route_callback']();
        }

        return parent::render($entity, $field);
    }
}
