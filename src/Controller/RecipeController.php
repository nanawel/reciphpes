<?php

namespace App\Controller;


use App\Document\Recipe;
use App\Form\RecipeType;

class RecipeController extends DocumentController
{
    protected function _getDocumentConfig($config = null) {
        return [
            'class'           => Recipe::class,
            'form_class'      => RecipeType::class,
            'type_id'         => 'App:Recipe',
            'type'            => 'recipe',
            'route_prefix'    => 'recipe',
            'template_prefix' => 'recipe',
        ];
    }
}
