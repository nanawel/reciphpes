<?php


namespace App\Controller;


use App\Entity\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public static function getSubscribedServices() {
        return parent::getSubscribedServices() + [
                'entity_manager' => 'Doctrine\ORM\EntityManagerInterface',
                'entity_registry' => 'App\Entity\Registry',
                'logger' => 'Psr\Log\LoggerInterface',
                'router' => 'Symfony\Component\Routing\Generator\UrlGeneratorInterface',
                'session' => 'Symfony\Component\HttpFoundation\Session\Session',
                'translator' => 'Symfony\Contracts\Translation\TranslatorInterface',
                'white_october_breadcrumbs' => 'WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs',
                'datatable_factory' => 'Omines\DataTablesBundle\DataTableFactory'
            ];
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager() {
        return $this->get('entity_manager');
    }

    /**
     * @return Registry
     */
    protected function getEntityRegistry() {
        return $this->get('entity_registry');
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function getLogger() {
        return $this->get('logger');
    }

    /**
     * @return \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    protected function getBreadcrumbs() {
        return $this->get('white_october_breadcrumbs');
    }

    /**
     * @return TranslatorInterface
     */
    protected function getTranslator() {
        return $this->get('translator');
    }

    /**
     * @return DataTableFactory
     */
    protected function getDataTableFactory() {
        return $this->get('datatable_factory');
    }
}
