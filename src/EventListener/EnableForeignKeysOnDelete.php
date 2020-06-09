<?php

namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Class EnableForeignKeysOnDelete
 *
 * @see https://github.com/doctrine/dbal/pull/2836
 */
class EnableForeignKeysOnDelete implements EventSubscriber
{
    /** @var bool|null */
    protected $fkEnabledByDefault;

    /** @var int */
    protected $callLevel = 0;

    public function preRemove(LifecycleEventArgs $args) {
        if ($args->getEntityManager()->getConnection()->getDatabasePlatform()->getName() != 'sqlite') {
            return;
        }

        $this->isFkEnabledByDefault($args->getEntityManager());
        if (! $this->callLevel && ! $this->isFkEnabledByDefault($args->getEntityManager())) {
            $this->callLevel++;
            $args->getEntityManager()
                ->createNativeQuery(
                    'PRAGMA foreign_keys = ON;',
                    new ResultSetMapping()
                )
                ->execute();
        }
    }

    public function postRemove(LifecycleEventArgs $args) {
        if ($args->getEntityManager()->getConnection()->getDatabasePlatform()->getName() != 'sqlite') {
            return;
        }

        $this->callLevel--;
        if (! $this->callLevel && ! $this->isFkEnabledByDefault($args->getEntityManager())) {
            $args->getEntityManager()
                ->createNativeQuery(
                    'PRAGMA foreign_keys = OFF;',
                    new ResultSetMapping()
                )
                ->execute();
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents() {
        return [
            Events::preRemove,
            Events::postRemove,
        ];
    }

    protected function isFkEnabledByDefault(EntityManagerInterface $entityManager) {
        if ($this->fkEnabledByDefault === null) {
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('foreign_keys', 'fk');
            $result = $entityManager->createNativeQuery('PRAGMA foreign_keys', $rsm)
                ->execute();
            if (! count($result)) {
                throw new \OutOfBoundsException('No row returned, cannot check for FK status.');
            }
            $this->fkEnabledByDefault = ! ! $result[0]['fk'];
        }

        return $this->fkEnabledByDefault;
    }
}
