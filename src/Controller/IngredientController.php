<?php

namespace App\Controller;


use App\Document\Ingredient;
use App\Form\IngredientType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @ParamConverter("document", class="App:Ingredient")
     *
     * @inheritDoc
     */
    public function show($document) {
        return parent::show($document);
    }

    /**
     * @ParamConverter("document", class="App:Ingredient", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $document = null) {
        return parent::edit($request, $document);
    }
}
