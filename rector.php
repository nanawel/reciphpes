<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
    ])
    ->withPhpSets(php83: true)
    ->withSymfonyContainerXml(
        __DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml'
    )
    ->withSets([
        \Rector\Symfony\Set\SymfonySetList::SYMFONY_64,
        \Rector\Symfony\Set\SymfonySetList::SYMFONY_CODE_QUALITY,
        \Rector\Symfony\Set\SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ])
    ->withPreparedSets(
        typeDeclarations: true,
        codeQuality: true,
        codingStyle: true,
        deadCode: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true,
    )
    ->withComposerBased(symfony: true)
    ->withoutParallel();
