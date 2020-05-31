<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    public function results(Request $request)
    {
        $query = $request->query->get('q');

        // Voir https://www.doctrine-project.org/projects/doctrine-mongodb-odm/en/2.0/cookbook/simple-search-engine.html#simple-search-engine

        return $this->render('search/results.html.twig', ['query' => $query]);
    }
}
