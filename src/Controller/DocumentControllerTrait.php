<?php

namespace App\Controller;

use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Markup;

/**
 * Trait DocumentControllerTrait
 *
 * @method DataTableFactory getDataTableFactory()
 * @method TranslatorInterface getTranslator()
 */
trait DocumentControllerTrait
{
    public function gridAction(Request $request) {
        $this->getBreadcrumbs()
            ->addItem(
                'breadcrumb.home',
                $this->get('router')->generate('index')
            )
            ->addItem(
                sprintf('breadcrumb.%s.grid', $this->getEntityConfig('type')),
                $this->get('router')->generate(sprintf('app_%s_grid', $this->getEntityConfig('route_prefix')))
            );

        /** @var DataTable $table */
        $table = $this->getDataTableFactory()->createFromType($this->getEntityConfig('datatable_type_class'))
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render(
            sprintf('%s/grid.html.twig', $this->getEntityConfig('template_prefix')),
            ['datatable' => $table]
        );
    }

    /**
     * @param object $entity
     */
    public function showBefore($entity) {
        $this->getBreadcrumbs()
            ->addItem(
                'breadcrumb.home',
                $this->get('router')->generate('index')
            )
            ->addItem(
                sprintf('breadcrumb.%s.grid', $this->getEntityConfig('type')),
                $this->get('router')->generate(sprintf('app_%s_grid', $this->getEntityConfig('route_prefix')))
            )
            ->addItem($entity->getName());
    }

    /**
     * @param object $entity
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($entity, $parameters = []) {
        $this->showBefore($entity);

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
            $entity = $this->newEntity($request);
        }
        $form = $this->createForm($this->getEntityConfig('form_class'), $entity);

        $this->getBreadcrumbs()
            ->addItem(
                'breadcrumb.home',
                $this->get('router')->generate('index')
            )
            ->addItem(
                sprintf('breadcrumb.%s.grid', $this->getEntityConfig('type')),
                $this->get('router')->generate(sprintf('app_%s_grid', $this->getEntityConfig('route_prefix')))
            )
            ->addItem(
                $entity->getId()
                    ? $entity->getName()
                    : sprintf('breadcrumb.%s.new', $this->getEntityConfig('type'))
            );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $success = false;
            try {
                $entity = $form->getData();

                $this->getEntityManager()->persist($entity);
                $this->getEntityManager()->flush();

                $message = new Markup(
                    $this->getTranslator()->trans(
                        '<strong>%name%</strong> saved successfully!&nbsp;<a href="%url%">Click here to see it.</a>',
                        [
                            '%name%' => htmlspecialchars($entity->getName(), ENT_QUOTES | ENT_SUBSTITUTE),
                            '%url%' => $this->get('router')->generate(
                                sprintf(
                                    'app_%s_show',
                                    $this->getEntityConfig('route_prefix')
                                ),
                                ['id' => $entity->getId()]
                            )
                        ]
                    ),
                    'utf-8'
                );
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $message
                );
                $success = true;
            } catch (\Throwable $e) {
                $this->getLogger()->error($e);
                $this->addFlash(
                    'danger',
                    $this->getTranslator()->trans('Unable to save element: %cause%', ['%cause%' => $e->getMessage()])
                );
            }

            if ($success) {
                return $this->redirectToRoute(
                    sprintf(
                        'app_%s_grid',
                        $this->getEntityConfig('route_prefix')
                    )
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

    protected function newEntity(Request $request) {
        $entityClass = $this->getEntityConfig('class');

        return new $entityClass();
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
