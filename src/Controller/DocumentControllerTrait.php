<?php

namespace App\Controller;

use App\Grid\Builder;
use Symfony\Component\HttpFoundation\Request;
use Twig\Markup;

trait DocumentControllerTrait
{
    public function gridAction(\App\Grid\Builder\Registry $registry, Request $request) {
        $this->getBreadcrumbs()
            ->addItem(
                $this->getTranslator()->trans('breadcrumb.home'),
                $this->get('router')->generate('index')
            )
            ->addItem(
                $this->getTranslator()->trans(sprintf('breadcrumb.%s.grid', $this->getEntityConfig('type'))),
                $this->get('router')->generate(sprintf('app_%s_grid', $this->getEntityConfig('type')))
            );

        /** @var Builder $gridBuilder */
        $gridBuilder = $registry->getGridBuilder($this->getEntityConfig('type'))
            ->withEntityConfig($this->getEntityConfig())
            ->withSearchQuery($request->get('q'));

        return $this->render(
            sprintf('%s/grid.html.twig', $this->getEntityConfig('template_prefix')),
            ['gridConfig' => $gridBuilder->build()]
        );
    }

    /**
     * @param object $entity
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($entity, $parameters = []) {
        $this->getBreadcrumbs()
            ->addItem(
                $this->getTranslator()->trans('breadcrumb.home'),
                $this->get('router')->generate('index')
            )
            ->addItem(
                $this->getTranslator()->trans(sprintf('breadcrumb.%s.grid', $this->getEntityConfig('type'))),
                $this->get('router')->generate(sprintf('app_%s_grid', $this->getEntityConfig('type')))
            )
            ->addItem($entity->getName());

        return $this->render(
            sprintf('%s/show.html.twig', $this->getEntityConfig('template_prefix')),
            [
                'entity' => $entity,
                $this->getEntityConfig('type') => $entity,
            ] + $parameters
        );
    }

    /**
     * @param Request $request
     * @param object|null $entity
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, object $entity = null) {

        if (! $entity) {
            $entityClass = $this->getEntityConfig('class');
            $entity = new $entityClass();
        }
        $form = $this->createForm($this->getEntityConfig('form_class'), $entity);

        $this->getBreadcrumbs()
            ->addItem(
                $this->getTranslator()->trans('breadcrumb.home'),
                $this->get('router')->generate('index')
            )
            ->addItem(
                $this->getTranslator()->trans(sprintf('breadcrumb.%s.grid', $this->getEntityConfig('type'))),
                $this->get('router')->generate(sprintf('app_%s_grid', $this->getEntityConfig('type')))
            )
            ->addItem(
                $entity->getId()
                    ? $entity->getName()
                    : $this->getTranslator()->trans(sprintf('breadcrumb.%s.new', $this->getEntityConfig('type')))
            );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $success = false;
            try {
                $entity = $form->getData();

                $this->getEntityManager()->persist($entity);
                $this->getEntityManager()->flush();

                $message = new Markup(
                    sprintf(
                        'Élément enregistré avec succès ! '
                        . '<a href="%s"><i class="fas fa-link"></i> Cliquez ici pour le voir.</a>',
                        $this->get('router')->generate(
                            sprintf(
                                'app_%s_show',
                                $this->getEntityConfig('route_prefix')
                            ),
                            ['id' => $entity->getId()]
                        )
                    ), 'utf-8'
                );
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $message
                );
                $success = true;
            } catch (\Throwable $e) {
                $this->getLogger()->error($e);
                $this->addFlash('danger', "Impossible d'enregistrer l'élément : {$e->getMessage()}");
            }

            if ($success) {
                return $this->redirectToRoute(
                    sprintf(
                        'app_%s_grid',
                        $this->getEntityConfig('route_prefix')
                    ),
                    ['id' => $entity->getId()]
                );
            }
        }

        return $this->render(
            sprintf('%s/edit.html.twig', $this->getEntityConfig('template_prefix')),
            [
                $this->getEntityConfig('type') => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param object $entity
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, object $entity) {
        try {
            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();

            $this->addFlash('success', 'Élément supprimé avec succès.');
        } catch (\Throwable $e) {
            $this->addFlash('danger', "Impossible de supprimer l'élément : {$e->getMessage()}");

            return $this->redirectToRoute(
                sprintf(
                    'app_%s_show',
                    $this->getEntityConfig('route_prefix')
                ),
                ['id' => $entity->getId()]
            );
        }

        return $this->redirectToRoute(
            sprintf(
                'app_%s_grid',
                $this->getEntityConfig('route_prefix')
            ),
            ['id' => $entity->getId()]
        );
    }

    /**
     * @param null|string $config
     * @return mixed|array
     */
    protected function getEntityConfig($config = null) {
        return $config === null
            ? $this->_getEntityConfig()
            : $this->_getEntityConfig()[$config] ?? null;
    }
}
