<?php

namespace App\DataTable\Type;

use App\DataTable\Column\FieldWithLink;
use App\Entity\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\DataTable;

class Location extends AbstractEntity
{
    public function __construct(
        EntityManagerInterface $entityManager,
        Registry $entityRegistry,
        $entityType = null
    ) {
        parent::__construct($entityManager, $entityRegistry, $entityType ?? 'location');
    }

    public function configure(DataTable $dataTable, array $options) {
        parent::configure($dataTable, $options);
        $dataTable
            ->setName('locations-dt')
            ->add(
                'name',
                FieldWithLink::class,
                [
                    'className' => 'col-name',
                    'field' => 'location.name',
                    'link_route' => 'app_location_show',
                ]
            )
            ->addOrderBy('name');
        $this->addDefaultActions($dataTable, $options);
    }
}
