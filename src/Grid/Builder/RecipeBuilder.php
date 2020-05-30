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

        $this->columns['name'] = new TwigColumn(
            $this->twig,
            '_grid/field_with_link.html.twig',
            'app_recipe_show'
        );

        return $this;
    }
}
