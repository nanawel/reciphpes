<?php

namespace App\Form;

use App\Entity\TimeOfYear;
use App\Form\DataTransformer\LocationToIdTransformer;
use App\Form\DataTransformer\TagsToJsonTransformer;
use App\Form\Type\RecipeIngredientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        TagsToJsonTransformer $tagsToJsonTransformer,
        LocationToIdTransformer $locationToIdTransformer
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->tagsToJsonTransformer = $tagsToJsonTransformer;
        $this->locationToIdTransformer = $locationToIdTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom',
                    'required' => false,
                    'attr' => [
                        'autocomplete' => 'recipe_name',
                        'autofocus' => true
                    ],
                ]
            )
            ->add(
                'tags',
                TextType::class,
                [
                    'label' => 'Tags',
                    'required' => false,
                    'attr' => [
                        'class' => 'autocomplete-tag',
                        'data-fetch-url' => $this->router->generate('app_recipe_tag_search')
                    ],
                ]
            )
            ->add(
                'location',
                EntityType::class,
                [
                    'label' => 'Emplacement',
                    'help' => 'Facultatif',
                    'class' => \App\Entity\Location::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Choisissez un emplacement...',
                    'required' => false,
                ]
            )
            ->add(
                'locationDetails',
                TextType::class,
                [
                    'label' => 'Emplacement (détails)',
                    'help' => 'N° de page, chemin, etc.',
                    'required' => false,
                    'attr' => [
                        'autocomplete' => 'recipe_location_details',
                    ],
                ]
            )
            ->add(
                'timesOfYear',
                EntityType::class,
                [
                    'label' => "Époque(s) de l'année",
                    'help' => 'Facultatif',
                    'class' => TimeOfYear::class,
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'label_attr' => ['class' => 'checkbox-custom'],
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
            ->add(
                'recipeIngredients',
                CollectionType::class,
                [
                    'help' => 'Astuce : vous pouvez utiliser Ctrl+Entrée pour ajouter une nouvelle ligne.',
                    'block_name' => 'recipe_ingredients',
                    'entry_type' => RecipeIngredientType::class,
                    'label' => 'Ingrédients',
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'by_reference' => false,
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
                        'autocomplete' => 'recipe_ingredients',
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Enregistrer',
                    'attr' => [
                        'class' => 'btn-primary save-btn'
                    ],
                ]
            );

        $builder->get('tags')
            ->addModelTransformer($this->tagsToJsonTransformer);
        $builder->get('location')
            ->addModelTransformer($this->locationToIdTransformer);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(
            [
                'attr' => [
                    'class' => 'no-submit-on-enter'
                ]
            ]
        );
    }
}
