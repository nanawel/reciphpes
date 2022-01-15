<?php

namespace App\Controller;

use App\Service\AccessManager;
use Symfony\Component\HttpFoundation\Request;

class SignoutController extends AbstractController
{
    /** @var AccessManager */
    private $accessManager;

    public function __construct(
        AccessManager $accessManager
    ) {
        $this->accessManager = $accessManager;
    }

    public function index(
        Request $request
    ) {
        $redirect = $this->redirectToRoute('index');
        $this->accessManager->signOut($redirect);

        $this->addFlash('info', $this->getTranslator()->trans("You have signed out sucessfully."));

        return $redirect;
    }
}
