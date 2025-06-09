<?php


namespace App\Controller;


use App\Entity\Registry;
use App\Service\AccessManager;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public function __construct(
        private readonly \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs  $breadcrumbs,
        private readonly \Symfony\Contracts\Translation\TranslatorInterface $dataCollectorTranslator,
        private readonly \Symfony\Component\Routing\RouterInterface         $router
    )
    {
    }

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
                'access_manager' => \App\Service\AccessManager::class,
                'datatable_factory' => \Omines\DataTablesBundle\DataTableFactory::class,
                'entity_manager' => \Doctrine\ORM\EntityManagerInterface::class,
                'entity_registry' => \App\Entity\Registry::class,
                'logger' => \Psr\Log\LoggerInterface::class,
                'router' => \Symfony\Component\Routing\Generator\UrlGeneratorInterface::class,
                'request_stack' => \Symfony\Component\HttpFoundation\RequestStack::class,
                'translator' => \Symfony\Contracts\Translation\TranslatorInterface::class,
                'white_october_breadcrumbs' => \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs::class,
            ];
    }

    /**
     * @return AccessManager
     */
    protected function getAccessManager() {
        return $this->container->get('access_manager');
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager() {
        return $this->container->get('entity_manager');
    }

    /**
     * @return Registry
     */
    protected function getEntityRegistry() {
        return $this->container->get('entity_registry');
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function getLogger() {
        return $this->container->get('logger');
    }

    /**
     * @return \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    protected function getBreadcrumbs() {
        return $this->breadcrumbs;
    }

    /**
     * @return TranslatorInterface
     */
    protected function getTranslator()
    {
        return $this->dataCollectorTranslator;
    }

    /**
     * @return DataTableFactory
     */
    protected function getDataTableFactory()
    {
        return $this->container->get('datatable_factory');
    }

    /**
     * @return UrlGeneratorInterface
     */
    protected function getRouter()
    {
        return $this->router;
    }

    /**
     * @return Session
     */
    protected function getSession()
    {
        return $this->container->get('request_stack')->getSession();
    }
}
