<?php


namespace App\Form\Type;


use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class RecipeIngredientType extends AbstractType implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RouterInterface */
    protected $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom',
                    'attr' => [
                        'class' => 'jq-autocomplete',
                        'placeholder' => 'Nom...',
                        'autocomplete' => 'ingredient_name',
                        'data-autocomplete-bak' => 'ingredient_name',
                        'data-fetch-url' => $this->router->generate('app_ingredient_search'),
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'note',
                TextType::class,
                [
                    'label' => 'Note',
                    'attr' => [
                        'placeholder' => 'Note',
                        'autocomplete' => 'ingredient_note',
                    ],
                    'required' => false,
                    'help' => "Précision complémentaire sur l'ingrédient (quantité, etc.)"
                ]
            )
            ->resetViewTransformers()
            ->addViewTransformer($this);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(
            [
                'label_attr' => [
                    'class' => 'd-none'
                ],
                'class' => RecipeIngredient::class,
                'block_name' => 'recipe_ingredients',
                'compound' => true,
                'multiple' => true,
                'choices' => [],
            ]
        );
    }

    /**
     * @param RecipeIngredient $recipeIngredient
     * @return array
     */
    public function transform($recipeIngredient) {
        if (empty($recipeIngredient)) {
            return [];
        }

        return [
            'name' => $recipeIngredient->getName(),
            'note' => $recipeIngredient->getNote(),
        ];
    }

//    public function transformToArray($recipeIngredients) {
//        if (! $recipeIngredients) {
//            return [];
//        }
//        if (! is_array($recipeIngredients)) {
//            $recipeIngredients = $recipeIngredients->toArray();
//        }
//
//        /** @var RecipeIngredient[] $recipeIngredients */
//        return array_reduce(
//            $recipeIngredients,
//            function ($carry, $recipeIngredient) {
//                return array_merge(
//                    $carry,
//                    [
//                        [
//                            'id' => $recipeIngredient->getIngredient()->getId(),
//                            'value' => $recipeIngredient->getIngredient()->getName()
//                        ]
//                    ]
//                );
//            },
//            []
//        );
//    }

    public function reverseTransform($json) {
//        $data = json_decode($json, JSON_OBJECT_AS_ARRAY);
//        if (! $data) {
//            return [];
//        }
//
//        $return = [];
//        foreach ($data as $ingredientData) {
//            $recipeIngredient = new RecipeIngredient();
//            if (! empty($ingredientData['id'])) {
//                $ingredient = $this->entityManager->getRepository(Ingredient::class)->find($ingredientData['id']);
//                $recipeIngredient->setIngredient($ingredient);
//            }
//            else {
//                $recipeIngredient->setIngredient((new Ingredient())->setName($ingredientData['value']));
//            }
//            $return[] = $recipeIngredient;
//        }
//
//        return $return;
    }

    public function getParent() {
        return EntityType::class;
    }

}
