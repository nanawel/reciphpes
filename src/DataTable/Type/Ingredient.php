<?php

namespace App\DataTable\Type;

use App\DataTable\Column\FieldWithLink;
use App\Entity\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;

class Ingredient extends AbstractEntity
{
    public function __construct(
        EntityManagerInterface $entityManager,
        Registry $entityRegistry,
        $entityType = null
    ) {
        parent::__construct($entityManager, $entityRegistry, $entityType ?? 'ingredient');
    }

    public function configure(DataTable $dataTable, array $options) {
        parent::configure($dataTable, $options);
        $dataTable
            ->setName('ingredients-dt')
            ->add(
                'name',
                FieldWithLink::class,
                [
                    'className' => 'col-name',
                    'field' => 'ingredient.name',
                    'link_route' => 'app_ingredient_show',
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
