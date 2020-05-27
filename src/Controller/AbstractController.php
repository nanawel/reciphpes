<?php


namespace App\Controller;


abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public static function getSubscribedServices() {
        return parent::getSubscribedServices() + [
            'doctrine_mongodb.odm.document_manager' => 'Doctrine\ODM\MongoDB\DocumentManager',
            'dtc_grid.manager.source' => 'Dtc\GridBundle\Manager\GridSourceManager',
            'dtc_grid.renderer.factory' => 'Dtc\GridBundle\Grid\Renderer\RendererFactory',
        ];
    }
}
