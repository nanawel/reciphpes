<?php

namespace App\Grid\Builder;

use App\Grid\Builder;
use App\Grid\Column\Action;
use App\Grid\Column\ColumnInterface;
use App\Grid\Column\DefaultColumn;
use App\Grid\Column\TwigColumn;
use App\Grid\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Twig\Environment;

class RecipeBuilder extends DefaultBuilder
{
    public function reset() {
        parent::reset();

        $this->headers = [
            'name' => 'Nom',
            'location' => 'Emplacement',
            'locationDetails' => 'Détails',
        ];

        $this->columns['name'] = new TwigColumn(
            $this->twig,
            '_grid/field_with_link.html.twig',
            ['link_route' => 'app_recipe_show']
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
