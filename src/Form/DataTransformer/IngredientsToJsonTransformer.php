<?php


namespace App\Form\DataTransformer;


use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IngredientsToJsonTransformer implements DataTransformerInterface
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
     * @param \Doctrine\Common\Collections\Collection|array $ingredients
     * @return array JSON
     */
    public function transformToArray($ingredients) {
        if (! $ingredients) {
            return [];
        }
        if (! is_array($ingredients)) {
            $ingredients = $ingredients->toArray();
        }

        return array_reduce(
            $ingredients,
            function ($carry, $ingredient) {
                return array_merge(
                    $carry,
                    [['id' => $ingredient->getId(), 'value' => $ingredient->getName()]]
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
            if (! empty($ingredientData['id'])) {
                $return[] = $this->entityManager->getRepository(Ingredient::class)->find($ingredientData['id']);
            }
            else {
                $return[] = (new Ingredient())->setName($ingredientData['value']);
            }
        }

        return $return;
    }
}
