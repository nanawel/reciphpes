<?php

namespace App\Controller;

use App\Service\AccessManager;
use Symfony\Component\HttpFoundation\Request;

class SignoutController extends AbstractController
{
    public function __construct(private readonly AccessManager $accessManager)
    {
    }

    public function index(
        Request $request
    )
    {
        $redirect = $this->redirectToRoute('index');
        $this->accessManager->signOut($redirect);

        $this->addFlash('info', $this->getTranslator()->trans("You have signed out sucessfully."));

        return $redirect;
    }
}
