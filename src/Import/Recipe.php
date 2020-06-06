<?php

namespace App\Import;

use App\Entity\AbstractEntity;
use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;

class Recipe extends AbstractImport
{
    /** @var Ingredient[] */
    protected $ingredientsCache;

    /**
     * @param AbstractEntity $entity
     * @param string $property
     * @param mixed $value
     */
    protected function setEntityProperty(AbstractEntity $entity, string $property, $value) {
        if ($property == 'recipeIngredients' && ($recipeId = $entity->getId())) {
            // Force reload all RecipeIngredient instance from EM to correctly trigger INSERT or UPDATE
            foreach ($value as $idx => $incompleteRecipeIngredient) {
                if ($ingredientId = $incompleteRecipeIngredient->getIngredient()->getId()) {
                    /** @var RecipeIngredient $recipeIngredient */
                    $recipeIngredient = $this->entityManager->getRepository(RecipeIngredient::class)
                        ->findOneBy(
                            [
                                'recipe' => $entity,
                                'ingredient' => $incompleteRecipeIngredient->getIngredient()
                            ]
                        );
                    // Sync additional fields
                    $recipeIngredient->setNote($incompleteRecipeIngredient->getNote());

                    // Replace RI by the one reloaded from EM
                    $value[$idx] = $recipeIngredient;
                }
            }
        }
        else {
            $this->propertyAccessor->setValue($entity, $property, $value);
        }
    }

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
