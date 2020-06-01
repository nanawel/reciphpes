<?php

namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class LocationController extends DocumentController
{
    protected function _getEntityConfig($config = null) {
        return $this->getEntityRegistry()->getEntityConfig('location', $config);
    }

    /**
     * @ParamConverter("entity", class="App:Location")
     *
     * @inheritDoc
     */
    public function show($entity) {
        return parent::show($entity);
    }

    /**
     * @ParamConverter("entity", class="App:Location", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $entity = null) {
        return parent::edit($request, $entity);
    }

    /**
     * @ParamConverter("entity", class="App:Recipe", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $entity = null) {
        return parent::delete($request, $entity);
    }
}
