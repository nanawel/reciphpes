<?php

namespace App\DataTable\Type;

use App\DataTable\Column\FieldWithLink;
use App\Entity\Registry;
use Omines\DataTablesBundle\DataTable;

class Ingredient extends AbstractEntity
{
    public function __construct(
        Registry $entityRegistry,
        $entityType = null
    ) {
        parent::__construct($entityRegistry, $entityType ?? 'ingredient');
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
            ->addOrderBy('name');
        $this->addDefaultActions($dataTable, $options);
    }
}
