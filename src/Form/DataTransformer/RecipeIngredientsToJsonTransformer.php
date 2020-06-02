<?php


namespace App\Form\DataTransformer;


use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RecipeIngredientsToJsonTransformer implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|array $tags
     * @return string JSON
     */
    public function transform($tags) {
        return json_encode($this->transformToArray($tags));
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|array $recipeIngredients
     * @return array JSON
     */
    public function transformToArray($recipeIngredients) {
        if (! $recipeIngredients) {
            return [];
        }
        if (! is_array($recipeIngredients)) {
            $recipeIngredients = $recipeIngredients->toArray();
        }

        /** @var RecipeIngredient[] $recipeIngredients */
        return array_reduce(
            $recipeIngredients,
            function ($carry, $recipeIngredient) {
                return array_merge(
                    $carry,
                    [
                        [
                            'id' => $recipeIngredient->getIngredient()->getId(),
                            'value' => $recipeIngredient->getIngredient()->getName()
                        ]
                    ]
                );
            },
            []
        );
    }

    /**
     * @param string $json
     * @return array
     * @throws TransformationFailedException
     */
    public function reverseTransform($json) {
        $data = json_decode($json, JSON_OBJECT_AS_ARRAY);
        if (! $data) {
            return [];
        }

        $return = [];
        foreach ($data as $ingredientData) {
            $recipeIngredient = new RecipeIngredient();
            if (! empty($ingredientData['id'])) {
                $ingredient = $this->entityManager->getRepository(Ingredient::class)->find($ingredientData['id']);
                $recipeIngredient->setIngredient($ingredient);
            }
            else {
                $recipeIngredient->setIngredient((new Ingredient())->setName($ingredientData['value']));
            }
            $return[] = $recipeIngredient;
        }

        return $return;
    }
}
