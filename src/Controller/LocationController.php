<?php

namespace App\Controller;


use App\Grid\Builder;
use App\Grid\Builder\Registry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class LocationController extends AbstractController
{
    use DocumentControllerTrait;

    protected function _getEntityConfig($config = null) {
        return $this->getEntityRegistry()->getEntityConfig('location', $config);
    }

    /**
     * @inheritDoc
     */
    public function grid(\App\Grid\Builder\Registry $registry, Request $request) {
        return $this->gridAction($registry, $request);
    }

    /**
     * @ParamConverter("entity", class="App:Location")
     *
     * @inheritDoc
     */
    public function show(Registry $registry, Request $request, $entity) {
        $this->showBefore($entity);

        /** @var Builder $gridBuilder */
        $recipeGridBuilder = $registry->getGridBuilder('recipe')
            ->withEntityConfig($this->getEntityRegistry()->getEntityConfig('recipe'))
            ->withSearchQuery($request->get('q'))
            ->withSearchCriteria(['location' => $entity]);

        return $this->render(
            sprintf('%s/show.html.twig', $this->getEntityConfig('template_prefix')),
            [
                'entity' => $entity,
                $this->getEntityConfig('type') => $entity,
                'recipeGridConfig' => $recipeGridBuilder->build()
            ]
        );
    }

    /**
     * @ParamConverter("entity", class="App:Location", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $entity = null) {
        return $this->editAction($request, $entity);
    }

    /**
     * @ParamConverter("entity", class="App:Location", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $entity = null) {
        return $this->deleteAction($request, $entity);
    }
}
