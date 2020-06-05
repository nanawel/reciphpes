<?php


namespace App\Form\Type;


use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class RecipeIngredientType extends AbstractType implements DataMapperInterface
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
            ->setDataMapper($this);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(
            [
                'label_attr' => [
                    'class' => 'd-none'     // Hide label (placeholder displayed only)
                ],
                'class' => RecipeIngredient::class,
                'block_name' => 'recipe_ingredients',
                'compound' => true
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function mapDataToForms($viewData, $forms) {
        if (null === $viewData) {
            return;
        }

        // invalid data type
        if (! $viewData instanceof RecipeIngredient) {
            throw new Exception\UnexpectedTypeException($viewData, RecipeIngredient::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $forms['name']->setData($viewData->getIngredient()->getName());
        $forms['note']->setData($viewData->getNote());
    }

    /**
     * @inheritDoc
     */
    public function mapFormsToData($forms, &$viewData) {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $name = trim($forms['name']->getData());
        $note = trim($forms['note']->getData());

        if (! strlen($name)) {
            return;
        }

        $ingredient = $this->entityManager->getRepository(Ingredient::class)
            ->findOneBy(['name' => $name]);

        if (! $ingredient) {
            $ingredient = (new Ingredient())
                ->setName($name);
        }
        if (! $viewData) {
            $viewData = new RecipeIngredient();
        }

        $viewData->setIngredient($ingredient)
            ->setNote($note);
    }

//    public function getParent() {
//        return EntityType::class;
//    }
}
