<?php

namespace App\Controller;


use App\DataTable\Adapter\Doctrine\ORM\AutomaticQueryBuilder;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\DataTable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class TagController extends AbstractController
{
    use DocumentControllerTrait;

    protected function _getEntityConfig($config = null) {
        return $this->getEntityRegistry()->getEntityConfig('tag', $config);
    }

    /**
     * @inheritDoc
     */
    public function grid(Request $request) {
        return $this->gridAction($request);
    }

    /**
     * @ParamConverter("entity", class="App\Entity\Tag")
     *
     * @inheritDoc
     */
    public function show(Request $request, $entity) {
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
                    function (QueryBuilder $builder) use ($entity) {
                        $builder->distinct()
                            ->innerJoin('recipe.tags', 't')
                            ->where('t = :tag')->setParameter('tag', $entity);
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
     * @ParamConverter("entity", class="App\Entity\Tag", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $entity = null) {
        return $this->editAction($request, $entity);
    }

    /**
     * @ParamConverter("entity", class="App\Entity\Tag", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $entity = null) {
        return $this->deleteAction($request, $entity);
    }
}
