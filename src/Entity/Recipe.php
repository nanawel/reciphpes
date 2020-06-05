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
     * @ORM\ManyToMany(targetEntity="App\Entity\TimeOfYear")
     * @ORM\JoinTable(name="recipe_timeofyear",
     *      joinColumns={@ORM\JoinColumn(name="recipe_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="timeofyear_id", referencedColumnName="id")}
     * )
     */
    protected $timesOfYear;

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
        $this->timesOfYear = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setLocationDetails($locationDetails) {
        $this->locationDetails = $locationDetails;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimesOfYear() {
        return $this->timesOfYear;
    }

    /**
     * @param mixed $timesOfYear
     * @return $this
     */
    public function setTimesOfYear($timesOfYear) {
        $this->timesOfYear = $timesOfYear;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecipeIngredients() {
        return $this->recipeIngredients;
    }

    /**
     * @param RecipeIngredient $recipeIngredient
     * @return $this
     */
    public function addRecipeIngredient($recipeIngredient): Recipe {
        if (! $this->recipeIngredients->contains($recipeIngredient)) {
            $recipeIngredient->setRecipe($this);
            $this->recipeIngredients->add($recipeIngredient);
        }

        return $this;
    }

    /**
     * @param RecipeIngredient $recipeIngredient
     * @return $this
     */
    public function removeRecipeIngredient($recipeIngredient): Recipe {
        if ($this->recipeIngredients->contains($recipeIngredient)) {
            $recipeIngredient->setRecipe(null);
            $this->recipeIngredients->removeElement($recipeIngredient);
        }

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
     * @return $this
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
     * @return $this
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }
}
