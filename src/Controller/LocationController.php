<?php

namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class LocationController extends DocumentController
{
    protected function _getDocumentConfig($config = null) {
        return $this->getDocumentRegistry()->getDocumentConfig('location', $config);
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

    /**
     * @ParamConverter("document", class="App:Recipe", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $document = null) {
        return parent::delete($request, $document);
    }
}
