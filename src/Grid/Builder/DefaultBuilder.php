<?php

namespace App\Grid\Builder;

use App\Grid\Builder;
use App\Grid\Column\Action;
use App\Grid\Column\ColumnInterface;
use App\Grid\Column\DefaultColumn;
use App\Grid\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class DefaultBuilder implements Builder
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Environment */
    protected $twig;

    /** @var TranslatorInterface */
    protected $translator;

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

    /** @var array */
    protected $searchCriteria = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        Environment $twig,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->reset();
    }

    public function withEntityConfig(array $entityConfig) {
        $this->entityConfig = $entityConfig;

        return $this;
    }

    public function withSearchQuery(?string $query) {
        if ($query = trim($query)) {
            $this->searchQuery = $query;
        }

        return $this;
    }

    public function withSearchCriteria(?array $criteria) {
        $this->searchCriteria = array_filter($criteria ?? []);

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
        $headers = $this->getHeaders() ?: $this->getDefaultHeaders();
        $this->gridConfiguration
            ->setHeaders($headers)
            ->setColumns($this->getColumns(array_keys($headers)))
            ->setItems($this->getItems());

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

    protected function getHeaders() {
        return array_reduce(
            $this->headers,
            function ($carry, $item) {
                return array_merge(
                    $carry,
                    [
                        $item => sprintf('grid.%s.header.%s', $this->getEntityConfig('type'), $item)
                    ]
                );
            },
            []
        );
    }

    protected function getDefaultHeaders() {
        return [
            'name' => sprintf('grid.%s.header.name', $this->getEntityConfig('type')),
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
        $actions[] = (new Action($this->twig, $this->translator))
            ->setLabel('View')
            ->setRoute(sprintf('app_%s_show', $this->getEntityConfig('type')))
            ->setClass('show btn-primary')
            ->setSortOrder(10);
        $actions[] = (new Action($this->twig, $this->translator))
            ->setLabel('Edit')
            ->setRoute(sprintf('app_%s_edit', $this->getEntityConfig('type')))
            ->setClass('edit btn-primary')
            ->setSortOrder(20);
//        $actions[] = (new Action($this->twig, $this->translator))
//            ->setLabel('Delete')
//            ->setRoute(sprintf('app_%s_delete', $this->getEntityConfig('type')))
//            ->setClass('delete btn-danger btn-delete')
//            ->setSortOrder(30);

        return $actions;
    }

    protected function getItems() {
        $repository = $this->entityManager->getRepository($this->getEntityConfig('class'));

        // TODO Use search + filters at the same time
        if ($this->searchQuery) {
            return $repository->search($this->searchQuery);
        }
        if ($this->searchCriteria) {
            return $repository->findBy($this->searchCriteria, ['name' => 'asc']);
        }

        return $repository->findAll();
    }
}
