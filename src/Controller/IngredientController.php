<?php

namespace App\Controller;


use App\Document\Ingredient;
use App\Form\IngredientType;

class IngredientController extends DocumentController
{
    protected function _getDocumentConfig($config = null) {
        return [
            'class'           => Ingredient::class,
            'form_class'      => IngredientType::class,
            'type_id'         => 'App:Ingredient',
            'type'            => 'ingredient',
            'route_prefix'    => 'ingredient',
            'template_prefix' => 'ingredient',
        ];
    }
}
