<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(
 *     repositoryClass=App\Repository\IngredientRepository::class
 * )
 * @ORM\Table(
 *     name="ingredient",
 *     indexes={
 *         @ORM\Index(name="INGREDIENT_NAME_IDX", columns={"name"})
 *     }
 * )
 */
class Ingredient extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string", length=255) */
    protected $name;

//    /**
//     * @ORM\ManyToMany(
//     *     targetEntity="App\Entity\RecipeIngredient",
//     *     mappedBy="recipe_id",
//     *     inversedBy="ingredients"
//     * )
//     */
//    protected $recipes;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Ingredient
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Ingredient
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

//    /**
//     * @return mixed
//     */
//    public function getRecipes() {
//        return $this->recipes;
//    }
//
//    /**
//     * @param mixed $recipes
//     * @return Ingredient
//     */
//    public function setRecipes($recipes) {
//        $this->recipes = $recipes;
//        return $this;
//    }

    /**
     * @return mixed
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return Ingredient
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
        return $this;
    }
}
