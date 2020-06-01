<?php


namespace App\Form\DataTransformer;


use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IngredientsToIdsTransformer implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $ingredients
     * @return array
     */
    public function transform($ingredients) {
        if (null === $ingredients) {
            return [];
        }

        return array_column($ingredients->toArray(), 'id');
    }

    /**
     * @param array $ingredientIds
     * @return array
     * @throws TransformationFailedException
     */
    public function reverseTransform($ingredientIds) {
        if (! $ingredientIds) {
            return null;
        }

        /** @var Ingredient[]|null $ingredients */
        return $this->entityManager
            ->getRepository(Ingredient::class)
            ->findAllByIds($ingredientIds)
            ->toArray();
    }
}
