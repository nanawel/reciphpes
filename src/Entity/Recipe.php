<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(
 *     repositoryClass=App\Repository\RecipeRepository::class
 * )
 * @ORM\Table(
 *     name="recipe",
 *     indexes={
 *         @ORM\Index(name="RECIPE_NAME_IDX", columns={"name"}),
 *         @ORM\Index(name="RECIPE_INSTRUCTIONS_IDX", columns={"instructions"}, flags={"fulltext"})
 *     }
 * )
 */
class Recipe extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string", length=255) */
    protected $name;

    /** @ORM\ManyToMany(targetEntity="App\Entity\Tag", cascade={"persist"}) */
    protected $tags;

    /** @ORM\ManyToOne(targetEntity="App\Entity\Location") */
    protected $location;

    /** @ORM\Column(type="string", length=255, name="location_details", nullable=true) */
    protected $locationDetails;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\RecipeIngredient",
     *     mappedBy="recipe",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    protected $recipeIngredients;

    /** @ORM\Column(type="text", nullable=true) */
    protected $instructions;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    public function __construct() {
        $this->recipeIngredients = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Recipe
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
     * @return Recipe
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     * @return Recipe
     */
    public function setTags($tags) {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @param mixed $location
     * @return Recipe
     */
    public function setLocation($location) {
        $this->location = $location;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationDetails() {
        return $this->locationDetails;
    }

    /**
     * @param mixed $locationDetails
     * @return Recipe
     */
    public function setLocationDetails($locationDetails) {
        $this->locationDetails = $locationDetails;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecipeIngredients() {
        return $this->recipeIngredients;
    }

    /**
     * @param mixed $recipeIngredients
     * @return Recipe
     */
    public function setRecipeIngredients($recipeIngredients = null): Recipe {
        $this->recipeIngredients = $recipeIngredients;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstructions() {
        return $this->instructions;
    }

    /**
     * @param mixed $instructions
     * @return Recipe
     */
    public function setInstructions($instructions) {
        $this->instructions = $instructions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return Recipe
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
        return $this;
    }
}
