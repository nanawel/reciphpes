<?php

namespace App\Document;


class Registry
{
    protected $documentConfigurations;

    public function __construct(
        array $documentConfigurations
    ) {
        $this->documentConfigurations = $documentConfigurations;
    }

    /**
     * @param string $documentType
     * @param null|string $config
     * @return mixed|array
     */
    public function getDocumentConfig($documentType, $config = null) {
        return $config === null
            ? $this->documentConfigurations[$documentType]
            : $this->documentConfigurations[$documentType][$config] ?? null;
    }
}
