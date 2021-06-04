<?php

namespace App\Controller;

use App\DataTable\Adapter\Doctrine\ORM\AutomaticQueryBuilder;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\DataTable;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    public function results(
        \App\Entity\Registry $entityRegistry,
        Request $request
    ) {
        $this->getBreadcrumbs()
            ->addItem(
                'breadcrumb.home',
                $this->get('router')->generate('index')
            )
            ->addItem(
                'breadcrumb.search_results',
                $this->get('router')->generate('app_search_results')
            );

        if ($request->getMethod() == 'POST') {
            // FIXME Ugly workaround - See https://github.com/omines/datatables-bundle/issues/160
            parse_str(parse_url($request->headers->get('referer'), PHP_URL_QUERY), $queryParts);
            $query = $queryParts['q'] ?? null;
        }
        else {
            $query = $request->query->get('q');
        }

        $terms = array_filter(
            preg_split('/\s+/', $query),
            function ($w) {
                return strlen($w) > 0;
            }
        );

        /** @var DataTable $table */
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
                    function (QueryBuilder $builder) use ($terms) {
                        $builder->distinct()
                            ->leftJoin('recipe.tags', 't');

                        foreach ($terms as $w => $term) {
                            $builder->andWhere("(recipe.name LIKE :term{$w}) OR (t.name LIKE :term{$w})")
                                ->setParameter("term{$w}", "%$term%");
                        }
                    },
                    new SearchCriteriaProvider(),
                ],
            ]
        );
        $recipeDatatable->handleRequest($request);
        if ($recipeDatatable->isCallback()) {
            return $recipeDatatable->getResponse();
        }

        /** @var DataTable $table */
        $ingredientDatatable = $this->getDataTableFactory()
            ->createFromType($this->getEntityRegistry()->getEntityConfig('ingredient', 'datatable_type_class'));
        $ingredientDatatable->getAdapter()->configure(
            [
                'entity' => $this->getEntityRegistry()->getEntityConfig('ingredient', 'class'),
                'query' => [
                    new AutomaticQueryBuilder(
                        $this->getEntityManager(),
                        $this->getEntityManager()->getClassMetadata(
                            $this->getEntityRegistry()->getEntityConfig('ingredient', 'class')
                        )
                    )
                ],
                'criteria' => [
                    function (QueryBuilder $builder) use ($terms) {
                        $builder->distinct();

                        foreach ($terms as $w => $term) {
                            $builder->andWhere("(ingredient.name LIKE :term{$w})")
                                ->setParameter("term{$w}", "%$term%");
                        }
                    },
                    new SearchCriteriaProvider(),
                ],
            ]
        );
        $ingredientDatatable->handleRequest($request);
        if ($ingredientDatatable->isCallback()) {
            return $ingredientDatatable->getResponse();
        }

        /** @var DataTable $table */
        $locationDatatable = $this->getDataTableFactory()
            ->createFromType($this->getEntityRegistry()->getEntityConfig('location', 'datatable_type_class'));
        $locationDatatable->getAdapter()->configure(
            [
                'entity' => $this->getEntityRegistry()->getEntityConfig('location', 'class'),
                'query' => [
                    new AutomaticQueryBuilder(
                        $this->getEntityManager(),
                        $this->getEntityManager()->getClassMetadata(
                            $this->getEntityRegistry()->getEntityConfig('location', 'class')
                        )
                    )
                ],
                'criteria' => [
                    function (QueryBuilder $builder) use ($terms) {
                        $builder->distinct();

                        foreach ($terms as $w => $term) {
                            $builder->andWhere("(location.name LIKE :term{$w})")
                                ->setParameter("term{$w}", "%$term%");
                        }
                    },
                    new SearchCriteriaProvider(),
                ],
            ]
        );
        $locationDatatable->handleRequest($request);
        if ($locationDatatable->isCallback()) {
            return $locationDatatable->getResponse();
        }

        return $this->render(
            'search/results.html.twig',
            [
                'query' => $query,
                'recipeDatatable' => $recipeDatatable,
                'ingredientDatatable' => $ingredientDatatable,
                'locationDatatable' => $locationDatatable,
            ]
        );
    }
}
