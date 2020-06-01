<?php


namespace App\Controller;


use App\Entity\Registry;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public static function getSubscribedServices() {
        return parent::getSubscribedServices() + [
                'router' => 'Symfony\Component\Routing\Generator\UrlGeneratorInterface',
                'session' => 'Symfony\Component\HttpFoundation\Session\Session',
                'entity_registry' => 'App\Entity\Registry'
            ];
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager() {
        return $this->get('doctrine_mongodb.odm.entity_manager');
    }

    /**
     * @return Registry
     */
    protected function getEntityRegistry() {
        return $this->get('entity_registry');
    }
}
