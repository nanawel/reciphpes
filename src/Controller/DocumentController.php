<?php


namespace App\Controller;


use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class DocumentController extends AbstractController
{
    public function index() {
        $renderer = $this->get('dtc_grid.renderer.factory')->create('datatables');
        /** @var \Dtc\GridBundle\Manager\GridSourceManager $gridSourceManager */
        $gridSourceManager = $this->get('dtc_grid.manager.source');
        $gridSource = $gridSourceManager->get($this->getDocumentConfig('type_id'));
        $renderer->bind($gridSource);

        return $this->render(
            sprintf('%s/grid.html.twig', $this->getDocumentConfig('template_prefix')),
            $renderer->getParams()
        );
    }

    /**
     * ADMIN / DEBUG ONLY
     */
    public function grid() {
        $renderer = $this->get('dtc_grid.renderer.factory')->create('datatables');
        $gridSource = $this->get('dtc_grid.manager.source')->get($this->getDocumentConfig('type_id'));
        $renderer->bind($gridSource);

        return $this->render('@DtcGrid/Page/datatables.html.twig', $renderer->getParams());
    }

    public function edit(Request $request) {
        $documentClass = $this->getDocumentConfig('class');
        $document = new $documentClass();
        $form = $this->createForm($this->getDocumentConfig('form_class'), $document);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $document = $form->getData();

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
            ));
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
