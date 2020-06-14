<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    public function results(
        \App\Entity\Registry $entityRegistry,
        \App\Grid\Builder\Registry $gridRegistry,
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

        $query = $request->query->get('q');

        $recipesGridConfig = $gridRegistry->getGridBuilder($entityRegistry->getEntityConfig('recipe', 'type'))
            ->withEntityConfig($entityRegistry->getEntityConfig('recipe'))
            ->withSearchQuery($query)
            ->build();
        $ingredientsGridConfig = $gridRegistry->getGridBuilder(
            $entityRegistry->getEntityConfig('ingredient', 'type')
        )
            ->withEntityConfig($entityRegistry->getEntityConfig('ingredient'))
            ->withSearchQuery($query)
            ->build();
        $locationsGridConfig = $gridRegistry->getGridBuilder($entityRegistry->getEntityConfig('location', 'type'))
            ->withEntityConfig($entityRegistry->getEntityConfig('location'))
            ->withSearchQuery($query)
            ->build();

        return $this->render(
            'search/results.html.twig',
            [
                'query' => $query,
                'grids' => [
                    'recipes' => [
                        'template' => '_grid.html.twig',
                        'gridConfig' => $recipesGridConfig
                    ],
                    'ingredients' => [
                        'template' => '_grid.html.twig',
                        'gridConfig' => $ingredientsGridConfig
                    ],
                    'locations' => [
                        'template' => '_grid.html.twig',
                        'gridConfig' => $locationsGridConfig
                    ],
                ]
            ]
        );
    }
}
