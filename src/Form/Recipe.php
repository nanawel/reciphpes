<?php


namespace App\Form;


use App\Form\DataTransformer\IngredientsToIdsTransformer;
use App\Form\DataTransformer\LocationToIdTransformer;
use App\Form\DataTransformer\TagsArrayToStringTransformer;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class Recipe extends AbstractType
{
    /** @var DocumentManager */
    protected $documentManager;

    /** @var RouterInterface */
    protected $router;

    /** @var LocationToIdTransformer */
    protected $tagsArrayToStringTransformer;

    /** @var LocationToIdTransformer */
    protected $locationToIdTransformer;

    /** @var IngredientsToIdsTransformer */
    protected $ingredientsToIdsTransformer;

    public function __construct(
        DocumentManager $documentManager,
        RouterInterface $router,
        TagsArrayToStringTransformer $tagsArrayToStringTransformer,
        LocationToIdTransformer $locationToIdTransformer,
        IngredientsToIdsTransformer $ingredientsToIdsTransformer
    ) {
        $this->documentManager = $documentManager;
        $this->router = $router;
        $this->tagsArrayToStringTransformer = $tagsArrayToStringTransformer;
        $this->locationToIdTransformer = $locationToIdTransformer;
        $this->ingredientsToIdsTransformer = $ingredientsToIdsTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $locations = [];
        foreach ($this->documentManager->getRepository(\App\Document\Location::class)->findAll() as $location) {
            $locations[$location->getName()] = $location->getId();
        }
        $ingredients = [];
        foreach ($this->documentManager->getRepository(\App\Document\Ingredient::class)->findAll() as $ingredient) {
            $ingredients[$ingredient->getName()] = $ingredient->getId();
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
                        'class' => 'autocomplete autocomplete-recipe-tags',
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
                    'required' => false
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
                ChoiceType::class,
                [
                    'label' => 'Ingrédients',
                    'choices' => $ingredients,
                    'expanded' => true,
                    'multiple' => true,
                    'placeholder' => 'Choisissez des ingrédients...',
                    'help' => 'Facultatif. Plusieurs valeurs possibles.',
                    'required' => false
                ]
            )
            ->add(
                'instructions',
                TextareaType::class,
                [
                    'label' => 'Instructions',
                    'help' => 'Facultatif.',
                    'required' => false
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Enregistrer']);

        $builder->get('tags')
            ->addModelTransformer($this->tagsArrayToStringTransformer);
        $builder->get('location')
            ->addModelTransformer($this->locationToIdTransformer);
        $builder->get('ingredients')
            ->addModelTransformer($this->ingredientsToIdsTransformer);
    }
}
