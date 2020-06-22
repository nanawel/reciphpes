<?php

namespace App\Controller;

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

        /** @var DataTable $table */
        $recipeDatatable = $this->getDataTableFactory()
            ->createFromType($this->getEntityRegistry()->getEntityConfig('recipe', 'datatable_type_class'));
        $recipeDatatable->getAdapter()->configure(
            [
                'entity' => $this->getEntityRegistry()->getEntityConfig('recipe', 'class'),
                'criteria' => [
                    function (QueryBuilder $builder) use ($query) {
                        $builder->distinct()
                            ->leftJoin('recipe.tags', 't')
                            ->where('recipe.name LIKE :query')
                            ->orWhere('t.name LIKE :query')
                            ->setParameter('query', "%$query%");
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
                'criteria' => [
                    function (QueryBuilder $builder) use ($query) {
                        $builder->distinct()
                            ->where('ingredient.name LIKE :query')
                            ->setParameter('query', "%$query%");
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
                'criteria' => [
                    function (QueryBuilder $builder) use ($query) {
                        $builder->distinct()
                            ->where('location.name LIKE :query')
                            ->setParameter('query', "%$query%");
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
