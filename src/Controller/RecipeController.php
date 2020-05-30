<?php

namespace App\Controller;


use App\Document\Recipe;
use App\Form\RecipeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @ParamConverter("document", class="App:Recipe")
     *
     * @inheritDoc
     */
    public function show($document) {
        return parent::show($document);
    }

    /**
     * @ParamConverter("document", class="App:Recipe", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $document = null) {
        return parent::edit($request, $document);
    }
}
