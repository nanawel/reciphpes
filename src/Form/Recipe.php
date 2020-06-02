<?php


namespace App\Form;


use App\Form\DataTransformer\LocationToIdTransformer;
use App\Form\DataTransformer\RecipeIngredientsToJsonTransformer;
use App\Form\DataTransformer\TagsToJsonTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class Recipe extends AbstractType
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var RouterInterface */
    protected $router;

    /** @var TagsToJsonTransformer */
    protected $tagsToJsonTransformer;

    /** @var LocationToIdTransformer */
    protected $locationToIdTransformer;

    /** @var RecipeIngredientsToJsonTransformer */
    protected $recipeIngredientsToJsonTransformer;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        TagsToJsonTransformer $tagsToJsonTransformer,
        LocationToIdTransformer $locationToIdTransformer,
        RecipeIngredientsToJsonTransformer $recipeIngredientsToJsonTransformer
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->tagsToJsonTransformer = $tagsToJsonTransformer;
        $this->locationToIdTransformer = $locationToIdTransformer;
        $this->recipeIngredientsToJsonTransformer = $recipeIngredientsToJsonTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $locations = [];
        foreach ($this->entityManager->getRepository(\App\Entity\Location::class)->findAll() as $location) {
            $locations[$location->getName()] = $location->getId();
        }

        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom',
                    'required' => false,
                ]
            )
            ->add(
                'tags',
                TextType::class,
                [
                    'label' => 'Tags',
                    'required' => false,
                    'attr' => [
                        'class' => 'autocomplete',
                        'data-fetch-url' => $this->router->generate('app_recipe_tags_search')
                    ],
                ]
            )
            ->add(
                'location',
                ChoiceType::class,
                [
                    'label' => 'Emplacement',
                    'choices' => $locations,
                    'placeholder' => 'Choisissez un emplacement...',
                    'help' => 'Facultatif',
                    'required' => false,
                ]
            )
            ->add(
                'locationDetails',
                TextType::class,
                [
                    'label' => 'Emplacement (détails)',
                    'help' => 'N° de page, chemin, etc.',
                    'required' => false
                ]
            )
            ->add(
                'ingredients',
                TextType::class,
                [
                    'label' => 'Ingrédients',
                    'required' => false,
                    'attr' => [
                        'class' => 'autocomplete',
                        'data-fetch-url' => $this->router->generate('app_recipe_ingredients_search')
                    ],
                ]
            )
            ->add(
                'instructions',
                TextareaType::class,
                [
                    'label' => 'Instructions',
                    'help' => 'Facultatif.',
                    'required' => false,
                    'attr' => [
                        'class' => 'markdown',
                    ],
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Enregistrer']);

        $builder->get('tags')
            ->addModelTransformer($this->tagsToJsonTransformer);
        $builder->get('location')
            ->addModelTransformer($this->locationToIdTransformer);
        $builder->get('ingredients')
            ->addModelTransformer($this->recipeIngredientsToJsonTransformer);
    }
}
