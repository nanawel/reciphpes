<?php

namespace App\Controller;


use App\Entity\Ingredient;
use App\Form\DataTransformer\IngredientsToJsonTransformer;
use App\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class IngredientController extends AbstractController
{
    use DocumentControllerTrait;

    protected function _getEntityConfig($config = null) {
        return $this->getEntityRegistry()->getEntityConfig('ingredient', $config);
    }

    /**
     * @inheritDoc
     */
    public function grid(\App\Grid\Builder\Registry $registry, Request $request) {
        return $this->gridAction($registry, $request);
    }

    /**
     * @ParamConverter("entity", class="App:Ingredient")
     *
     * @inheritDoc
     */
    public function show($entity) {
        return $this->showAction($entity);
    }

    /**
     * @ParamConverter("entity", class="App:Ingredient", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $entity = null) {
        return $this->editAction($request, $entity);
    }

    /**
     * @ParamConverter("entity", class="App:Ingredient", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $entity = null) {
        return $this->deleteAction($request, $entity);
    }

    public function search(Request $request, IngredientsToJsonTransformer $ingredientsToJsonTransformer) {
        $term = $request->get('term');

        /** @var TagRepository $repository */
        $repository = $this->getEntityManager()->getRepository(Ingredient::class);

        $result = $ingredientsToJsonTransformer->transformToArray($repository->findLike($term));

        return $this->json($result);
    }
}
