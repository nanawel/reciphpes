<?php

namespace App\Grid\Builder;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Grid\Builder;
use App\Grid\Column\Action;
use App\Grid\Column\ColumnInterface;
use App\Grid\Column\DefaultColumn;
use App\Grid\Column\TwigColumn;
use App\Grid\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Twig\Environment;

class RecipeBuilder extends DefaultBuilder
{
    public function reset() {
        parent::reset();

        $this->headers = [
            'name' => 'Nom',
            'tags' => 'Tags',
            'location' => 'Emplacement',
            'locationDetails' => 'Détails',
        ];

        $this->columns['name'] = new TwigColumn(
            $this->twig,
            '_grid/field_with_link.html.twig',
            ['link_route' => 'app_recipe_show']
        );
        $this->columns['tags'] = new TwigColumn(
            $this->twig,
            '_grid/tags.html.twig',
            ['tag_class' => 'badge-secondary']
        );
        $this->columns['location'] = new TwigColumn(
            $this->twig,
            '_grid/location.html.twig'
        );
        $this->columns['locationDetails'] = new TwigColumn(
            $this->twig,
            '_grid/locationDetails.html.twig'
        );

        return $this;
    }

    protected function getItems() {
        if ($this->searchQuery) {
            $matchingIngredients = $this->entityManager->createQueryBuilder(Ingredient::class)
                ->text($this->searchQuery)
                ->getQuery()
                ->execute()
                ->toArray();

//            var_dump($matchingIngredients);

            $builder = $this->entityManager->createQueryBuilder($this->getEntityConfig('class'));
            $builder
                ->addOr($builder->expr()->text($this->searchQuery))
//                ->field('ingredients')->in($matchingIngredients)
                ->addOr(
//                    $builder->expr()->setQuery()->field('ingredients')->in($matchingIngredients)
//                    $builder->expr()->field('ingredients.$id')->in($matchingIngredientIds)
                );

            $query = $builder->getQuery();

            //var_dump(json_encode($query->debug(), JSON_PRETTY_PRINT));exit;

            $query->execute();
        }

        return parent::getItems();
    }
}
