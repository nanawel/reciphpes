<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    public function results(
        \App\Entity\Registry $entityRegistry,
        \App\Grid\Builder\Registry $gridRegistry,
        Request $request
    ) {
        $query = $request->query->get('q');

        $recipesGridConfig = $gridRegistry->getGridBuilder($entityRegistry->getEntityConfig('recipe', 'type'))
            ->withEntityConfig($entityRegistry->getEntityConfig('recipe'))
            ->withRequest($request)
            ->build();
        $ingredientsGridConfig = $gridRegistry->getGridBuilder(
            $entityRegistry->getEntityConfig('ingredient', 'type')
        )
            ->withEntityConfig($entityRegistry->getEntityConfig('ingredient'))
            ->withRequest($request)
            ->build();
        $locationsGridConfig = $gridRegistry->getGridBuilder($entityRegistry->getEntityConfig('location', 'type'))
            ->withEntityConfig($entityRegistry->getEntityConfig('location'))
            ->withRequest($request)
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
