<?php


namespace App\Form\DataTransformer;


use App\Document\Ingredient;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IngredientsToIdsTransformer implements DataTransformerInterface
{
    /** @var DocumentManager */
    private $documentManager;

    public function __construct(DocumentManager $documentManager) {
        $this->documentManager = $documentManager;
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
        return $this->documentManager
            ->getRepository(Ingredient::class)
            ->findAllByIds($ingredientIds)
            ->toArray();
    }
}
