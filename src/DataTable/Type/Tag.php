<?php

namespace App\DataTable\Type;

use App\DataTable\Column\FieldWithLink;
use App\Entity\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\DataTable;

class Tag extends AbstractEntity
{
    public function __construct(
        EntityManagerInterface $entityManager,
        Registry $entityRegistry,
        $entityType = null
    ) {
        parent::__construct($entityManager, $entityRegistry, $entityType ?? 'tag');
    }

    public function configure(DataTable $dataTable, array $options) {
        parent::configure($dataTable, $options);
        $dataTable
            ->setName('tags-dt')
            ->add(
                'name',
                FieldWithLink::class,
                [
                    'className' => 'col-name',
                    'field' => 'tag.name',
                    'link_route' => 'app_tag_show',
                ]
            )
            ->addOrderBy('name');
        $this->addDefaultActions($dataTable, $options);
    }
}
