<?php

namespace App\Controller;


use App\Entity\Tag;
use App\Form\DataTransformer\TagsToJsonTransformer;
use App\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class RecipeController extends AbstractController
{
    use DocumentControllerTrait {
        DocumentControllerTrait::newEntity as defaultNewEntity;
    }

    protected function _getEntityConfig($config = null) {
        return $this->getEntityRegistry()->getEntityConfig('recipe', $config);
    }

    /**
     * @inheritDoc
     */
    public function grid(\App\Grid\Builder\Registry $registry, Request $request) {
        return $this->gridAction($registry, $request);
    }

    /**
     * @ParamConverter("entity", class="App:Recipe")
     *
     * @inheritDoc
     */
    public function show($entity) {
        return $this->showAction($entity);
    }

    /**
     * @ParamConverter("entity", class="App:Recipe", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $entity = null) {
        return $this->editAction($request, $entity);
    }

    protected function newEntity(Request $request) {
        $entity = $this->defaultNewEntity($request);
        if ($locationId = trim($request->get('location_id'))) {
            $location = $this->getEntityManager()->getRepository(\App\Entity\Location::class)
                ->find($locationId);
            if ($location) {
                $entity->setLocation($location);
            }
        }

        return $entity;
    }

    /**
     * @ParamConverter("entity", class="App:Recipe", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $entity = null) {
        return $this->deleteAction($request, $entity);
    }

    public function searchTags(Request $request, TagsToJsonTransformer $tagsToJsonTransformer) {
        $term = $request->get('term');

        /** @var TagRepository $repository */
        $repository = $this->getEntityManager()->getRepository(Tag::class);

        $result = $tagsToJsonTransformer->transformToArray($repository->findLike($term));

        return $this->json($result);
    }
}
