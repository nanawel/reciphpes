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
use Twig\Markup;

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
    public function grid(Request $request) {
        return $this->gridAction($request);
    }

    /**
     * @ParamConverter("entity", class="App\Entity\Recipe")
     *
     * @inheritDoc
     */
    public function show($entity) {
        return $this->showAction($entity);
    }

    /**
     * @ParamConverter("entity", class="App\Entity\Recipe", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $entity = null) {
        return $this->editAction($request, $entity);
    }

    protected function newEntity(Request $request) {
        $entity = $this->defaultNewEntity($request);

        if (! $entity->getId()) {
            $formData = $this->prepareFormData($request);
            foreach ($formData as $field => $value) {
                // TODO Replace with Doctrine's method
                $setter = 'set' . ucfirst($field);
                $entity->{$setter}($value);
            }
        }

        return $entity;
    }

    /**
     * @ParamConverter("entity", class="App\Entity\Recipe", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $entity = null) {
        return $this->deleteAction($request, $entity);
    }

    public function massCreate(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        if (!$this->getAccessManager()->hasWriteAccess()) {
            $this->addFlash('danger', $this->getTranslator()->trans('Not allowed 😕 Please log in first.'));

            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(
            \App\Form\Recipe\MassCreate::class,
            $this->prepareFormData($request)
        );

        $this->getBreadcrumbs()
            ->addItem(
                'breadcrumb.home',
                $this->getRouter()->generate('index')
            )
            ->addItem(
                sprintf('breadcrumb.%s.grid', $this->getEntityConfig('type')),
                $this->getRouter()->generate(sprintf('app_%s_grid', $this->getEntityConfig('route_prefix')))
            )
            ->addItem(sprintf('breadcrumb.%s.masscreate', $this->getEntityConfig('type')));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $formData = $form->getData();

                /** @var Ingredient[] $newIngredients */
                $newIngredients = [];
                /** @var Tag[] $newTags */
                $newTags = [];

                /** @var Recipe $recipe */
                foreach ($formData['recipes'] as $recipe) {
                    $recipe->setLocation($formData['location']);
                    foreach ($formData['tags'] as $tag) {
                        $recipe->addTag($tag);
                    }

                    // Handle new tags present several times in the submitted form:
                    // once the first occurrence is saved, update subsequent recipes with this instance
                    /** @var Tag $tag */
                    foreach ($recipe->getTags() as $tag) {
                        if (! $tag->getId()) {
                            if (isset($newTags[strtolower((string)$tag->getName())])) {
                                // Replace with the first instance that should have an ID by now
                                $recipe->removeTag($tag)
                                    ->addTag($newTags[strtolower((string)$tag->getName())]);
                            } else {
                                $newTags[strtolower((string)$tag->getName())] = $tag;
                            }
                        }
                    }

                    $recipeIngredientIds = [];
                    // Handle new ingredients present several times in the submitted form:
                    // once the first occurrence is saved, update subsequent recipes with this instance
                    /** @var RecipeIngredient $recipeIngredient */
                    foreach ($recipe->getRecipeIngredients() as $recipeIngredient) {
                        if (! $recipeIngredient->getIngredient()->getId()) {
                            if (isset($newIngredients[strtolower((string)$recipeIngredient->getName())])) {
                                // Replace with the first instance that should have an ID by now
                                $recipeIngredient->setIngredient(
                                    $newIngredients[strtolower((string)$recipeIngredient->getName())]
                                );
                            } else {
                                $newIngredients[strtolower((string)$recipeIngredient->getName())]
                                    = $recipeIngredient->getIngredient();
                            }
                        }

                        // Also check that the same ingredient won't be added twice or more
                        if (! in_array($recipeIngredient->getIngredient()->getId(), $recipeIngredientIds)) {
                            $recipeIngredientIds[] = $recipeIngredient->getIngredient()->getId();
                        }
                        else {
                            // Duplicate => remove
                            $recipe->removeRecipeIngredient($recipeIngredient);
                        }
                    }

                    $this->getEntityManager()->persist($recipe);
                }

                $this->getEntityManager()->flush();

                // Prepare redirect URL with selected common location and/or tags
                $redirectParams = [];
                if ($location = $formData['location']) {
                    $redirectParams['location'] = $location->getId();
                }

                foreach ($formData['tags'] as $tag) {
                    $redirectParams['tags'][] = $tag->getId();
                }

                if ($form->get('saveAddMore')->isClicked()) {
                    $message = $this->getTranslator()->trans(
                        '%count% recipe(s) saved successfully!',
                        [
                            '%count%' => count($formData['recipes']),
                        ]
                    );
                    $this->getSession()->getFlashBag()->add(
                        'success',
                        new Markup($message, 'utf-8')
                    );

                    $redirect = $this->redirectToRoute('app_recipe_masscreate', $redirectParams);
                }
                else {
                    $message = $this->getTranslator()->trans(
                        '%count% recipe(s) saved successfully! <a href="%create_more_url%">Create more!</a>',
                        [
                            '%count%' => count($formData['recipes']),
                            '%create_more_url%' => $this->getRouter()->generate(
                                'app_recipe_masscreate',
                                $redirectParams
                            )
                        ]
                    );
                    $this->getSession()->getFlashBag()->add(
                        'success',
                        new Markup($message, 'utf-8')
                    );
                    $redirect = $this->redirectToRoute(sprintf('app_%s_grid', $this->getEntityConfig('route_prefix')));
                }
            } catch (\Throwable $e) {
                $this->getLogger()->error($e);
                $this->addFlash(
                    'danger',
                    $this->getTranslator()->trans('Unable to save elements: %cause%', ['%cause%' => $e->getMessage()])
                );
            }

            if (! empty($redirect)) {
                return $redirect;
            }
        }

        return $this->render(
            sprintf('%s/masscreate.html.twig', $this->getEntityConfig('template_prefix')),
            [
                'form' => $form,
            ]
        );
    }

    protected function prepareFormData(Request $request): array {
        $data = [];
        if (($location = (int)$request->get('location')) !== 0) {
            $data['location'] = $this->getEntityManager()->getRepository(Location::class)->find($location);
        }

        if ($tags = $request->get('tags')) {
            $data['tags'] = $this->getEntityManager()->getRepository(Tag::class)->findBy(['id' => $tags]);
        }

        return $data;
    }

    public function searchTags(Request $request, TagsToJsonTransformer $tagsToJsonTransformer): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $term = $request->get('term');

        /** @var TagRepository $repository */
        $repository = $this->getEntityManager()->getRepository(Tag::class);

        $result = $tagsToJsonTransformer->transformToArray($repository->findLike($term));

        return $this->json($result);
    }
}
