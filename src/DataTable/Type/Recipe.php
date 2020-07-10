<?php

namespace App\DataTable\Type;

use App\DataTable\Column\FieldWithLink;
use App\Entity\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;

class Recipe extends AbstractEntity
{
    public function __construct(
        EntityManagerInterface $entityManager,
        Registry $entityRegistry,
        $entityType = null
    ) {
        parent::__construct($entityManager, $entityRegistry, $entityType ?? 'recipe');
    }

    public function configure(DataTable $dataTable, array $options) {
        parent::configure($dataTable, $options);
        $dataTable
            ->setName('recipes-dt')
            ->add(
                'name',
                FieldWithLink::class,
                [
                    'className' => 'col-name',
                    'field' => 'recipe.name',
                    'link_route' => 'app_recipe_show',
                ]
            )
            ->add(
                'tags',
                TwigColumn::class,
                [
                    'className' => 'col-tags',
                    'orderable' => false,
                    'template' => '_datatables/column/tags.html.twig',
                ]
            )
            ->add(
                'location',
                TwigColumn::class,
                [
                    'className' => 'col-location',
                    'field' => 'location.name',
                    'template' => '_datatables/column/location.html.twig',
                ]
            )
            ->add(
                'locationDetails',
                TwigColumn::class,
                [
                    'className' => 'col-locationDetails',
                    'template' => '_datatables/column/locationDetails.html.twig',
                ]
            )
            ->add(
                'createdAt',
                TwigColumn::class,
                [
                    'className' => 'col-createdAt',
                    'template' => '_datatables/column/createdAt.html.twig',
                ]
            )
            ->addOrderBy('name');
        $this->addDefaultActions($dataTable, $options);
    }
}
