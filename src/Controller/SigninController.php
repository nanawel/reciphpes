<?php

namespace App\Controller;

use App\Service\AccessManager;
use Symfony\Component\HttpFoundation\Request;

class SigninController extends AbstractController
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

    public function form(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
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
                'form' => $form,
            ]
        );
    }
}
