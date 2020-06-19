<?php

namespace App\Form;

use App\Entity\TimeOfYear;
use App\Form\DataTransformer\LocationToIdTransformer;
use App\Form\DataTransformer\TagsToJsonTransformer;
use App\Form\Type\RecipeIngredientType;
use App\Repository\LocationRepository;
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

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        TagsToJsonTransformer $tagsToJsonTransformer
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->tagsToJsonTransformer = $tagsToJsonTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Name',
                    'required' => true,
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
                    'label' => 'Location',
                    'help' => 'Optional',
                    'required' => false,
                    'class' => \App\Entity\Location::class,
                    'query_builder' => function (LocationRepository $r) {
                        return $r->createQueryBuilder('l')
                            ->orderBy('l.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'placeholder' => 'Choose a location...',
                ]
            )
            ->add(
                'locationDetails',
                TextType::class,
                [
                    'label' => 'Location (details)',
                    'help' => 'Page #, path, etc.',
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
                    'label' => 'Time(s) of year',
                    'help' => 'Optional',
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
                    'help' => 'Hint: you can use Ctrl+Enter to add a new row.',
                    'block_name' => 'recipe_ingredients',
                    'entry_type' => RecipeIngredientType::class,
                    'label' => 'Ingredients',
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
                    'help' => 'Optional',
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
                    'label' => 'Save',
                    'attr' => [
                        'class' => 'btn-primary btn-save'
                    ],
                ]
            );

        $builder->get('tags')
            ->addModelTransformer($this->tagsToJsonTransformer);
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
