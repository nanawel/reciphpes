<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    public function results(Request $request)
    {
        $query = $request->query->get('q');

        return $this->render('search/results.html.twig', ['query' => $query]);
    }
}
