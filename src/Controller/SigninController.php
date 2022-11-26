<?php

namespace App\Controller;

use App\Service\AccessManager;
use Symfony\Component\HttpFoundation\Request;

class SigninController extends AbstractController
{
    /** @var AccessManager */
    private $accessManager;

    public function __construct(
        AccessManager $accessManager
    ) {
        $this->accessManager = $accessManager;
    }

    public function form(Request $request) {
        if ($this->accessManager->isSignedIn()) {
            $this->addFlash('info', "You are already signed in!");

            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(\App\Form\Signin::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->accessManager->signIn($form->get('password')->getData())) {
                $this->addFlash('info', $this->getTranslator()->trans("You are successfully signed in!"));

                return $this->redirectToRoute('index');
            }
            $this->addFlash('danger', $this->getTranslator()->trans("Invalid password 😕"));

            return $this->redirectToRoute('signin');
        }

        return $this->render(
            'signin.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
