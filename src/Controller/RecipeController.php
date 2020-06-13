<?php

namespace App\Controller;


use App\Entity\Ingredient;
use App\Entity\Location;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\Tag;
use App\Form\DataTransformer\TagsToJsonTransformer;
use App\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class RecipeController extends AbstractController
{
    use DocumentControllerTrait {
        DocumentControllerTrait::newEntity as defaultNewEntity;
    }

    protected function _getEntityConfig($config = null) {
        return $this->getEntityRegistry()->getEntityConfig('recipe', $config);
    }

    /**
     * @inheritDoc
     */
    public function grid(\App\Grid\Builder\Registry $registry, Request $request) {
        return $this->gridAction($registry, $request);
    }

    /**
     * @ParamConverter("entity", class="App:Recipe")
     *
     * @inheritDoc
     */
    public function show($entity) {
        return $this->showAction($entity);
    }

    /**
     * @ParamConverter("entity", class="App:Recipe", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $entity = null) {
        return $this->editAction($request, $entity);
    }

    protected function newEntity(Request $request) {
        $entity = $this->defaultNewEntity($request);
        if ($locationId = trim($request->get('location_id'))) {
            $location = $this->getEntityManager()->getRepository(\App\Entity\Location::class)
                ->find($locationId);
            if ($location) {
                $entity->setLocation($location);
            }
        }

        return $entity;
    }

    /**
     * @ParamConverter("entity", class="App:Recipe", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $entity = null) {
        return $this->deleteAction($request, $entity);
    }

    public function massCreate(Request $request) {
        $form = $this->createForm(
            \App\Form\Recipe\MassCreate::class,
            $this->massCreate_prepareFormData($request)
        );

        $this->getBreadcrumbs()
            ->addItem(
                'breadcrumb.home',
                $this->get('router')->generate('index')
            )
            ->addItem(
                sprintf('breadcrumb.%s.grid', $this->getEntityConfig('type')),
                $this->get('router')->generate(sprintf('app_%s_grid', $this->getEntityConfig('type')))
            )
            ->addItem(sprintf('breadcrumb.%s.masscreate', $this->getEntityConfig('type')));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $success = false;
            try {
                $formData = $form->getData();

                /** @var Ingredient[] $newIngredients */
                $newIngredients = [];

                /** @var Recipe $recipe */
                foreach ($formData['recipes'] as $recipe) {
                    $recipe->setLocation($formData['location']);
                    foreach ($formData['tags'] as $tag) {
                        $recipe->addTag($tag);
                    }

                    // Handle new ingredients present several times in the submitted form:
                    // once the first occurence is saved, update subsequent recipes with this instance
                    /** @var RecipeIngredient $recipeIngredient */
                    foreach ($recipe->getRecipeIngredients() as $recipeIngredient) {
                        if (! $recipeIngredient->getIngredient()->getId()) {
                            if (isset($newIngredients[$recipeIngredient->getName()])) {
                                // Replace with the first instance that should have an ID by now
                                $recipeIngredient->setIngredient($newIngredients[$recipeIngredient->getName()]);
                            }
                            else {
                                $newIngredients[$recipeIngredient->getName()] = $recipeIngredient->getIngredient();
                            }
                        }
                    }

                    $this->getEntityManager()->persist($recipe);
                }
                $this->getEntityManager()->flush();

                $message = $this->getTranslator()->trans(
                    '%count% recipe(s) saved successfully!',
                    [
                        '%count%' => count($formData['recipes'])
                    ]
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
                    $this->getTranslator()->trans('Unable to save elements: %cause%', ['%cause%' => $e->getMessage()])
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
            sprintf('%s/masscreate.html.twig', $this->getEntityConfig('template_prefix')),
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function massCreate_prepareFormData(Request $request): array {
        $data = [];
        if ($location = (int)$request->get('location')) {
            $data['location'] = $this->getEntityManager()->getRepository(Location::class)->find($location);
        }

        return $data;
    }

    public function searchTags(Request $request, TagsToJsonTransformer $tagsToJsonTransformer) {
        $term = $request->get('term');

        /** @var TagRepository $repository */
        $repository = $this->getEntityManager()->getRepository(Tag::class);

        $result = $tagsToJsonTransformer->transformToArray($repository->findLike($term));

        return $this->json($result);
    }
}
