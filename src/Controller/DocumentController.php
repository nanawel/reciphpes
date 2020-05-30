<?php

namespace App\Controller;

use App\Grid\Builder;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class DocumentController extends AbstractController
{
    public function index(\App\Grid\Builder\Registry $registry, Request $request) {
        /** @var Builder $gridBuilder */
        $gridBuilder = $registry->getGridBuilder($this->getDocumentConfig('type'))
            ->withDocumentConfig($this->getDocumentConfig())
            ->withRequest($request);

        return $this->render(
            sprintf('%s/grid.html.twig', $this->getDocumentConfig('template_prefix')),
            ['gridConfig' => $gridBuilder->build()]
        );
    }

    /**
     * @param object $document
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($document) {
        return $this->render(sprintf('%s/show.html.twig', $this->getDocumentConfig('template_prefix')),
            [
                'document' => $document,
                $this->getDocumentConfig('type') => $document,
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, object $document = null) {
        if (!$document) {
            $documentClass = $this->getDocumentConfig('class');
            $document = new $documentClass();
        }
        $form = $this->createForm($this->getDocumentConfig('form_class'), $document);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $document = $form->getData();
                if (!$document->getId()) {
                    $document->createdAt = time();
                }

                /** @var DocumentManager $entityManager */
                $entityManager = $this->get('doctrine_mongodb.odm.document_manager');
                $entityManager->persist($document);
                $entityManager->flush();
                $this->addFlash('success', "Nouvel élément enregistré avec succès !");
            }
            catch (\Throwable $e) {
                $this->addFlash('danger', "Impossible d'enregistrer l'élément : {$e->getMessage()}");
            }

            return $this->redirectToRoute(sprintf(
                'app_%s_edit',
                $this->getDocumentConfig('route_prefix')
            ), ['id' => $document->getId()]);
        }

        return $this->render(sprintf('%s/edit.html.twig', $this->getDocumentConfig('template_prefix')),
            [
                $this->getDocumentConfig('type') => $document,
                'form'                           => $form->createView(),
            ]
        );
    }

    /**
     * @param null|string $config
     * @return mixed|array
     */
    protected function getDocumentConfig($config = null) {
        return $config === null
            ? $this->_getDocumentConfig()
            : $this->_getDocumentConfig()[$config] ?? null;
    }

    abstract protected function _getDocumentConfig();
}
