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
                $this->getTranslator()->trans('breadcrumb.home'),
                $this->get('router')->generate('index')
            )
            ->addItem(
                $this->getTranslator()->trans('breadcrumb.search_results'),
                $this->get('router')->generate('search_results')
            );

        $query = $request->query->get('q');

        $recipesGridConfig = $gridRegistry->getGridBuilder($entityRegistry->getEntityConfig('recipe', 'type'))
            ->withEntityConfig($entityRegistry->getEntityConfig('recipe'))
            ->withSearchQuery($request)
            ->build();
        $ingredientsGridConfig = $gridRegistry->getGridBuilder(
            $entityRegistry->getEntityConfig('ingredient', 'type')
        )
            ->withEntityConfig($entityRegistry->getEntityConfig('ingredient'))
            ->withSearchQuery($request)
            ->build();
        $locationsGridConfig = $gridRegistry->getGridBuilder($entityRegistry->getEntityConfig('location', 'type'))
            ->withEntityConfig($entityRegistry->getEntityConfig('location'))
            ->withSearchQuery($request)
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
