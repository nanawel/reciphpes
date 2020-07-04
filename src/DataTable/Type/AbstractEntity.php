<?php

namespace App\DataTable\Type;

use App\DataTable\Adapter\Doctrine\ORM\AutomaticQueryBuilder;
use App\DataTable\Column\Actions;
use App\Entity\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;

abstract class AbstractEntity implements DataTableTypeInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Registry */
    protected $entityRegistry;

    /** @var string */
    protected $entityType;

    public function __construct(
        EntityManagerInterface $entityManager,
        Registry $entityRegistry,
        $entityType
    ) {
        $this->em = $entityManager;
        $this->entityRegistry = $entityRegistry;
        $this->entityType = $entityType;
    }

    public function configure(DataTable $dataTable, array $options) {
        $dataTable->createAdapter(
            ORMAdapter::class,
            [
                'entity' => $this->getEntityConfig('class'),
                'query' => [
                    new AutomaticQueryBuilder(
                        $this->em,
                        $this->em->getClassMetadata($this->getEntityConfig('class'))
                    )
                ]
            ]
        );
    }

    public function addDefaultActions(DataTable $dataTable, array $options) {
        $dataTable->add(
            'actions',
            Actions::class,
            [
                'className' => 'col-actions dt-right',
                'actions' => $this->getDefaultActions(),
            ]
        );
    }

    protected function getDefaultActions() {
        return [
            'show' => [
                'label' => 'View',
                'route' => sprintf('app_%s_show', $this->getEntityConfig('route_prefix')),
                'class' => 'btn-show btn-primary',
                'sort_order' => 10,
            ],
            'edit' => [
                'label' => 'Edit',
                'route' => sprintf('app_%s_edit', $this->getEntityConfig('route_prefix')),
                'class' => 'btn-edit btn-primary',
                'sort_order' => 20,
            ],
            'delete' => [
                'label' => 'Delete',
                'route' => sprintf('app_%s_delete', $this->getEntityConfig('route_prefix')),
                'class' => 'btn-delete btn-danger',
                'sort_order' => 30,
            ],

        ];
    }

    /**
     * @param null|string $config
     * @return mixed|array
     */
    protected function getEntityConfig($config = null) {
        return $config === null
            ? $this->entityRegistry->getEntityConfig($this->entityType)
            : $this->entityRegistry->getEntityConfig($this->entityType)[$config] ?? null;
    }
}
