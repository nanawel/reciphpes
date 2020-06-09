<?php

namespace App\Grid\Builder;

use App\Grid\Column\TwigColumn;

class RecipeBuilder extends DefaultBuilder
{
    public function reset() {
        parent::reset();

        $this->headers = [
            'name' => 'Nom',
            'tags' => 'Tags',
            'location' => 'Emplacement',
            'locationDetails' => 'Détails',
        ];

        $this->columns['name'] = new TwigColumn(
            $this->twig,
            '_grid/field_with_link.html.twig',
            ['link_route' => 'app_recipe_show']
        );
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
