<?php

namespace App\Controller;


use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Form\DataTransformer\IngredientsToJsonTransformer;
use App\Grid\Builder;
use App\Grid\Builder\Registry;
use App\Repository\TagRepository;
use Doctrine\ORM\QueryBuilder;
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
    public function show(Registry $registry, Request $request, $entity) {
        $this->showBefore($entity);

        /** @var QueryBuilder $qb */
        $qb = $this->getEntityManager()->getRepository(Recipe::class)->createQueryBuilder('r');
        $qb->leftJoin('r.recipeIngredients', 'ri')
            ->where('ri.ingredient = :ingredient_id')->setParameter('ingredient_id', $entity->getId());

        /** @var Builder $gridBuilder */
        $recipeGridBuilder = $registry->getGridBuilder('recipe')
            ->withEntityConfig($this->getEntityRegistry()->getEntityConfig('recipe'))
            ->withQueryBuilder($qb);

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
