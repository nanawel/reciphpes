<?php

namespace App\Controller;


use App\Entity\Recipe;
use App\Grid\Builder;
use App\Grid\Builder\Registry;
use Doctrine\ORM\QueryBuilder;
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
    public function grid(\App\Grid\Builder\Registry $registry, Request $request) {
        return $this->gridAction($registry, $request);
    }

    /**
     * @ParamConverter("entity", class="App:Tag")
     *
     * @inheritDoc
     */
    public function show(Registry $registry, Request $request, $entity) {
        /** @var QueryBuilder $qb */
        $qb = $this->getEntityManager()->getRepository(Recipe::class)->createQueryBuilder('r');
        $qb->innerJoin('r.tags', 't')
            ->where('t.id = :tag_id')->setParameter('tag_id', $entity->getId());

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
     * @ParamConverter("entity", class="App:Tag", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $entity = null) {
        return $this->editAction($request, $entity);
    }

    /**
     * @ParamConverter("entity", class="App:Tag", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $entity = null) {
        return $this->deleteAction($request, $entity);
    }
}
