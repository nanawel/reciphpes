<?php

namespace App\Controller;

use App\Entity\Recipe;

class HomeController extends AbstractController
{
    public function index() {
        return $this->render(
            'home.html.twig',
            [
                'recipeCount' => $this->getRecipeCount(),
                'todaysRecipe' => $this->getTodaysRecipe(),
                'latestRecipes' => $this->getLatestRecipes(),
            ]
        );
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function getRecipeCount() {
        return $this->getEntityManager()->createQueryBuilder()
            ->from(Recipe::class, 'r')
            ->select('count(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Recipe[]
     */
    protected function getLatestRecipes() {
        return $this->getEntityManager()->getRepository(Recipe::class)
            ->findBy([], ['createdAt' => 'desc'], 10);
    }

    /**
     * @return Recipe|null
     */
    protected function getTodaysRecipe() {
        $recipeIds = array_column(
            $this->getEntityManager()->createQueryBuilder()
                ->select('r.id')
                ->from(Recipe::class, 'r')
                ->getQuery()
                ->execute(),
            'id'
        );
        sort($recipeIds);

        $recipeId = $recipeIds[floor(time() / 86400) % count($recipeIds)];

        return $this->getEntityManager()->getRepository(Recipe::class)
            ->find($recipeId);
    }
}
