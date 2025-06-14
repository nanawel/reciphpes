<?php

namespace App\Controller;


use App\DataTable\Adapter\Doctrine\ORM\AutomaticQueryBuilder;
use App\Entity\Ingredient;
use App\Form\DataTransformer\IngredientsToJsonTransformer;
use App\Repository\IngredientRepository;
use App\Service\Transliterator;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\DataTable;
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
    public function grid(Request $request) {
        return $this->gridAction($request);
    }

    /**
     * @ParamConverter("entity", class="App\Entity\Ingredient")
     *
     * @inheritDoc
     */
    public function show(Request $request, $entity): \Symfony\Component\HttpFoundation\Response
    {
        $this->showBefore($entity);

        /** @var DataTable $recipeDatatable */
        $recipeDatatable = $this->getDataTableFactory()
            ->createFromType($this->getEntityRegistry()->getEntityConfig('recipe', 'datatable_type_class'));
        $recipeDatatable->getAdapter()->configure(
            [
                'entity' => $this->getEntityRegistry()->getEntityConfig('recipe', 'class'),
                'query' => [
                    new AutomaticQueryBuilder(
                        $this->getEntityManager(),
                        $this->getEntityManager()->getClassMetadata(
                            $this->getEntityRegistry()->getEntityConfig('recipe', 'class')
                        )
                    )
                ],
                'criteria' => [
                    function (QueryBuilder $builder) use ($entity): void {
                        $builder->distinct()
                            ->leftJoin('recipe.recipeIngredients', 'ri')
                            ->where('ri.ingredient = :ingredient')->setParameter('ingredient', $entity);
                    },
                    new SearchCriteriaProvider(),
                ],
            ]
        );
        $recipeDatatable->handleRequest($request);

        if ($recipeDatatable->isCallback()) {
            return $recipeDatatable->getResponse();
        }

        return $this->render(
            sprintf('%s/show.html.twig', $this->getEntityConfig('template_prefix')),
            [
                'entity' => $entity,
                $this->getEntityConfig('type') => $entity,
                'recipeDatatable' => $recipeDatatable
            ]
        );
    }

    /**
     * @ParamConverter("entity", class="App\Entity\Ingredient", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $entity = null) {
        return $this->editAction($request, $entity);
    }

    /**
     * @ParamConverter("entity", class="App\Entity\Ingredient", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $entity = null) {
        return $this->deleteAction($request, $entity);
    }

    public function search(Request $request, IngredientsToJsonTransformer $ingredientsToJsonTransformer): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $term = Transliterator::sortNameTransliterator()->transliterate($request->get('term'));

        /** @var IngredientRepository $repository */
        $repository = $this->getEntityManager()->getRepository(Ingredient::class);

        $result = $ingredientsToJsonTransformer->transformToArray($repository->findLike($term, 'sortName'));

        return $this->json($result);
    }
}
