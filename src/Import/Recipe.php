<?php

namespace App\Import;

use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;

class Recipe extends AbstractImport
{
    /** @var Ingredient[] */
    protected $ingredientsCache;

    /**
     * @inheritDoc
     */
    protected function setToManyAssociationFieldValue(
        array &$entityData,
        string $field,
        int $idx,
        string $subField,
        $value,
        array $entityAssociation
    ) {
        // Special handling for "recipeIngredients.name" (virtual field)
        if ($field == 'recipeIngredients' && $subField == 'name') {
            if (! array_key_exists($idx, $entityData[$field] ?? [])) {
                $entityData[$field][$idx] = new RecipeIngredient();
            }

            $entityData[$field][$idx]->setIngredient($this->getIngredient($subField, $value));
        }
        else {
            parent::setToManyAssociationFieldValue($entityData, $field, $idx, $subField, $value, $entityAssociation);
        }
    }

    /**
     * @param string $field
     * @param string $name
     * @return Ingredient
     */
    protected function getIngredient(string $field, string $name): Ingredient {
        if (! isset($this->ingredientsCache[$name])) {
            $this->ingredientsCache[$name] = $this->entityManager->getRepository(Ingredient::class)
                ->findOneBy([$field => $name]);
            if (! $this->ingredientsCache[$name]) {
                $this->ingredientsCache[$name] = (new Ingredient())->setName($name);
            }
        }

        return $this->ingredientsCache[$name];
    }
}
