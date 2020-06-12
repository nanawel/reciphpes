<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(
 *     repositoryClass=App\Repository\RecipeIngredientRepository::class
 * )
 * @ORM\Table(
 *     name="recipeingredient"
 * )
 */
class RecipeIngredient extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Recipe",
     *     inversedBy="recipeIngredients"
     * )
     */
    protected $recipe;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Ingredient",
     *     inversedBy="recipeIngredients",
     *     cascade={"persist"}
     * )
     */
    protected $ingredient;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $note;

    /**
     * @return mixed
     */
    public function getRecipe() {
        return $this->recipe;
    }

    /**
     * @param mixed $recipe
     * @return $this
     */
    public function setRecipe($recipe) {
        $this->recipe = $recipe;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIngredient() {
        return $this->ingredient;
    }

    /**
     * @param mixed $ingredient
     * @return $this
     */
    public function setIngredient($ingredient) {
        $this->ingredient = $ingredient;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNote() {
        return $this->note;
    }

    /**
     * @param mixed $note
     * @return $this
     */
    public function setNote($note) {
        $this->note = $note;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName() {
        return $this->ingredient ? $this->ingredient->getName() : null;
    }
}
