<?php


namespace App\Grid;


use App\Grid\Column\Action;
use App\Grid\Column\ColumnInterface;
use Symfony\Component\HttpFoundation\Request;

interface Builder
{
    /**
     * @param array $entityConfig
     * @return $this
     */
    public function withEntityConfig(array $entityConfig);

    /**
     * @param Request $request
     * @return $this
     */
    public function withRequest(Request $request);

    /**
     * @param string[] $headers
     * @return mixed
     */
    public function withHeaders(array $headers);

    /**
     * @param ColumnInterface[] $columns
     * @return mixed
     */
    public function withColumns(array $columns);

    /**
     * @param Action[] $actions
     * @return mixed
     */
    public function withActions(array $actions);

    /**
     * @return Configuration
     */
    public function build();

    /**
     * @return $this
     */
    public function reset();
}
