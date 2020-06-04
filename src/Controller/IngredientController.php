<?php

namespace App\Controller;


use App\Entity\Ingredient;
use App\Form\DataTransformer\IngredientsToJsonTransformer;
use App\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class IngredientController extends DocumentController
{
    protected function _getEntityConfig($config = null) {
        return $this->getEntityRegistry()->getEntityConfig('ingredient', $config);
    }

    /**
     * @ParamConverter("entity", class="App:Ingredient")
     *
     * @inheritDoc
     */
    public function show($entity) {
        return parent::show($entity);
    }

    /**
     * @ParamConverter("entity", class="App:Ingredient", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $entity = null) {
        return parent::edit($request, $entity);
    }

    /**
     * @ParamConverter("entity", class="App:Ingredient", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $entity = null) {
        return parent::delete($request, $entity);
    }

    public function search(Request $request, IngredientsToJsonTransformer $ingredientsToJsonTransformer) {
        $term = $request->get('term');

        /** @var TagRepository $repository */
        $repository = $this->getEntityManager()->getRepository(Ingredient::class);

        $result = $ingredientsToJsonTransformer->transformToArray($repository->findLike($term));

        return $this->json($result);
    }
}
