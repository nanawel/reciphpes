<?php

namespace App\Grid\Builder;

use App\Grid\Column\TwigColumn;

class RecipeBuilder extends DefaultBuilder
{
    public function reset() {
        parent::reset();

        $this->headers = [
            'name',
            'tags',
            'location',
            'locationDetails',
        ];

        $this->columns['tags'] = new TwigColumn(
            $this->twig,
            '_grid/tags.html.twig'
        );
        $this->columns['location'] = new TwigColumn(
            $this->twig,
            '_grid/location.html.twig'
        );
        $this->columns['locationDetails'] = new TwigColumn(
            $this->twig,
            '_grid/locationDetails.html.twig'
        );

        return $this;
    }
}
