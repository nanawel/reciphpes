<?php

namespace App\Controller;

use App\Service\AccessManager;

class SignoutController extends AbstractController
{
    public function __construct(
        \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs  $breadcrumbs,
        \Symfony\Contracts\Translation\TranslatorInterface $dataCollectorTranslator,
        \Symfony\Component\Routing\RouterInterface         $router,
        private readonly AccessManager                     $accessManager
    )
    {
        parent::__construct(
            $breadcrumbs,
            $dataCollectorTranslator,
            $router
        );
    }

    public function index()
    {
        $redirect = $this->redirectToRoute('index');
        $this->accessManager->signOut($redirect);
        $this->addFlash('info', $this->getTranslator()->trans("You have signed out successfully."));
        return $redirect;
    }
}
