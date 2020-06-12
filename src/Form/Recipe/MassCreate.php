<?php

namespace App\Form\Recipe;

use App\Entity\Recipe;
use App\Form\DataTransformer\LocationToIdTransformer;
use App\Form\DataTransformer\TagsToJsonTransformer;
use App\Form\Type\RecipeSummary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class MassCreate extends AbstractType
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
                'tags',
                TextType::class,
                [
                    'label' => 'Tags',
                    'help' => 'Optional. Will be applied to all created recipes below.',
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
                    'help' => 'Optional. Will be applied to all created recipes below.',
                    'required' => false,
                    'class' => \App\Entity\Location::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Choose a location...',
                ]
            )
            ->add(
                'recipes',
                CollectionType::class,
                [
                    'help' => 'Hint: you can use Ctrl+Enter to add a new row.',
                    'block_name' => 'recipes',
                    'entry_type' => RecipeSummary::class,
                    'label' => 'Recettes',
                    'required' => true,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'data' => [new Recipe()]  // Init with 1 empty recipe
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Save all',
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
