<?php

namespace App\Grid\Builder;

use App\Grid\Builder;
use App\Grid\Column\Action;
use App\Grid\Column\ColumnInterface;
use App\Grid\Column\DefaultColumn;
use App\Grid\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class DefaultBuilder implements Builder
{
    /** @var DocumentManager */
    protected $documentManager;

    /** @var Environment */
    protected $twig;

    /** @var Configuration */
    protected $gridConfiguration;

    /** @var array */
    protected $documentConfig;

    /** @var string[] */
    protected $headers = [];

    /** @var ColumnInterface[] */
    protected $columns = [];

    /** @var Action[]|null */
    protected $actions = [];

    public function __construct(
        DocumentManager $documentManager,
        Environment $twig
    ) {
        $this->documentManager = $documentManager;
        $this->twig = $twig;
        $this->reset();
    }

    public function withDocumentConfig(array $documentConfig) {
        $this->documentConfig = $documentConfig;

        return $this;
    }

    public function withRequest(Request $request) {
        // TODO

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
            ->setItems(
                $this->documentManager->getRepository(\App\Document\Recipe::class)->findAll()
            )
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
        $this->documentConfig = [];
        $this->headers = [];
        $this->columns = [];
        $this->actions = [];

        return $this;
    }

    /**
     * @param null|string $config
     * @return mixed|array
     */
    protected function getDocumentConfig($config = null) {
        return $config === null
            ? $this->documentConfig
            : $this->documentConfig[$config] ?? null;
    }

    protected function getDefaultHeaders() {
        return [
            'id' => 'ID',
            'name' => 'Nom',
        ];
    }

    protected function getColumns(array $headers) {
        $columns = $this->columns;
        foreach ($headers as $header) {
            if (!in_array($header, $columns)) {
                $columns[$header] = new DefaultColumn();
            }
        }

        return $columns;
    }

    protected function getDefaultActions() {
        $actions = [];
        $actions[] = (new Action($this->twig))
            ->setLabel('Voir')
            ->setRoute(sprintf('app_%s_show', $this->getDocumentConfig('type')))
            ->setClass('show btn-primary')
            ->setSortOrder(10);
        $actions[] = (new Action($this->twig))
            ->setLabel('Modifier')
            ->setRoute(sprintf('app_%s_edit', $this->getDocumentConfig('type')))
            ->setClass('edit btn-primary')
            ->setSortOrder(20);
        $actions[] = (new Action($this->twig))
            ->setLabel('Supprimer')
            ->setRoute(sprintf('app_%s_delete', $this->getDocumentConfig('type')))
            ->setClass('delete btn-danger')
            ->setSortOrder(30);

        return $actions;
    }
}
