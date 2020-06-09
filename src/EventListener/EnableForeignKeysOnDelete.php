<?php


namespace App\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Query\ResultSetMapping;

class EnableForeignKeysOnDelete implements EventSubscriber
{
    /** @var bool|null */
    protected $fkEnabledByDefault;

    /** @var int */
    protected $callStack = 0;

    public function preRemove(LifecycleEventArgs $args) {
        if ($args->getEntityManager()->getConnection()->getDatabasePlatform()->getName() != 'sqlite') {
            return;
        }

        $this->isFkEnabledByDefault($args->getEntityManager());
        if (! $this->callStack && ! $this->isFkEnabledByDefault($args->getEntityManager())) {
            $this->callStack++;
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

        $this->callStack--;
        if (! $this->callStack && ! $this->isFkEnabledByDefault($args->getEntityManager())) {
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
            $this->fkEnabledByDefault = ! ! $result[0]['fk'];
        }

        return $this->fkEnabledByDefault;
    }
}
