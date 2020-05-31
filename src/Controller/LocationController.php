<?php

namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class LocationController extends DocumentController
{
    protected function _getDocumentConfig($config = null) {
        return [
            'class' => \App\Document\Location::class,
            'form_class' => \App\Form\Location::class,
            'type_id' => 'App:Location',
            'type' => 'location',
            'route_prefix' => 'location',
            'template_prefix' => 'location',
        ];
    }

    /**
     * @ParamConverter("document", class="App:Location")
     *
     * @inheritDoc
     */
    public function show($document) {
        return parent::show($document);
    }

    /**
     * @ParamConverter("document", class="App:Location", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $document = null) {
        return parent::edit($request, $document);
    }
}
