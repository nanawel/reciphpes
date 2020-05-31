<?php

namespace App\Controller;

use App\Grid\Builder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    public function results(
        \App\Document\Registry $documentRegistry,
        \App\Grid\Builder\Registry $gridRegistry,
        Request $request
    ) {
        $query = $request->query->get('q');

        $recipesGridConfig = $gridRegistry->getGridBuilder($documentRegistry->getDocumentConfig('recipe', 'type'))
            ->withDocumentConfig($documentRegistry->getDocumentConfig('recipe'))
            ->withRequest($request)
            ->build();
        $ingredientsGridConfig = $gridRegistry->getGridBuilder(
            $documentRegistry->getDocumentConfig('ingredient', 'type')
        )
            ->withDocumentConfig($documentRegistry->getDocumentConfig('ingredient'))
            ->withRequest($request)
            ->build();
        $locationsGridConfig = $gridRegistry->getGridBuilder($documentRegistry->getDocumentConfig('location', 'type'))
            ->withDocumentConfig($documentRegistry->getDocumentConfig('location'))
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
