<?php

namespace App\Grid\Builder;

use App\Grid\Builder;
use App\Grid\Column\Action;
use App\Grid\Column\ColumnInterface;
use App\Grid\Column\DefaultColumn;
use App\Grid\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class DefaultBuilder implements Builder
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Environment */
    protected $twig;

    /** @var Configuration */
    protected $gridConfiguration;

    /** @var array */
    protected $entityConfig;

    /** @var string[] */
    protected $headers = [];

    /** @var ColumnInterface[] */
    protected $columns = [];

    /** @var Action[]|null */
    protected $actions = [];

    /** @var string|null */
    protected $searchQuery = null;

    public function __construct(
        EntityManagerInterface $entityManager,
        Environment $twig
    ) {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->reset();
    }

    public function withEntityConfig(array $entityConfig) {
        $this->entityConfig = $entityConfig;

        return $this;
    }

    public function withRequest(Request $request) {
        if ($query = trim($request->get('q'))) {
            $this->searchQuery = $query;
        }

        return $this;
    }

    public function withHeaders(array $headers) {
        $this->headers = $headers;

        return $this;
    }

    public function withColumns(array $columns) {
        $this->columns = $columns;

        return $this;
    }

    public function withActions(array $actions) {
        $this->actions = $actions;

        return $this;
    }

    public function build() {
        $headers = $this->headers ?: $this->getDefaultHeaders();
        $this->gridConfiguration
            ->setHeaders($headers)
            ->setColumns($this->getColumns(array_keys($headers)))
            ->setItems($this->getItems())
        ;

        $actions = $this->actions ?: $this->getDefaultActions();
        usort($this->actions, function(Action $a, Action $b) {
            return $a->getSortOrder() <= $b->getSortOrder();
        });
        $this->gridConfiguration->setActions($actions);

        return $this->gridConfiguration;
    }

    public function reset() {
        $this->gridConfiguration = new Configuration();
        $this->entityConfig = [];
        $this->headers = [];
        $this->columns = [];
        $this->actions = [];
        $this->searchQuery = null;

        return $this;
    }

    /**
     * @param null|string $config
     * @return mixed|array
     */
    protected function getEntityConfig($config = null) {
        return $config === null
            ? $this->entityConfig
            : $this->entityConfig[$config] ?? null;
    }

    protected function getDefaultHeaders() {
        return [
            //'id' => 'ID',
            'name' => 'Nom',
        ];
    }

    protected function getColumns(array $headers) {
        $columns = $this->columns;
        foreach ($headers as $header) {
            if (! array_key_exists($header, $columns)) {
                $columns[$header] = new DefaultColumn();
            }
        }

        return $columns;
    }

    protected function getDefaultActions() {
        $actions = [];
        $actions[] = (new Action($this->twig))
            ->setLabel('Voir')
            ->setRoute(sprintf('app_%s_show', $this->getEntityConfig('type')))
            ->setClass('show btn-primary')
            ->setSortOrder(10);
        $actions[] = (new Action($this->twig))
            ->setLabel('Modifier')
            ->setRoute(sprintf('app_%s_edit', $this->getEntityConfig('type')))
            ->setClass('edit btn-primary')
            ->setSortOrder(20);
//        $actions[] = (new Action($this->twig))
//            ->setLabel('Supprimer')
//            ->setRoute(sprintf('app_%s_delete', $this->getEntityConfig('type')))
//            ->setClass('delete btn-danger')
//            ->setSortOrder(30);

        return $actions;
    }

    protected function getItems() {
        if ($this->searchQuery) {
            return $this->entityManager->createQueryBuilder($this->getEntityConfig('class'))
                ->text($this->searchQuery)
                ->getQuery()
                ->execute()
                ->toArray();
        }

        return $this->entityManager->getRepository($this->getEntityConfig('class'))
            ->findAll();
    }
}
