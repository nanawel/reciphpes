<?php

namespace App\Entity;

use App\Service\Transliterator;
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
 * @ORM\HasLifecycleCallbacks
 */
class Ingredient extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string", length=255, unique=true, options={"collation": "NOCASE"}) */
    protected $name;

    /**
     * @ORM\Column(
     *     type="string",
     *     length=255,
     *     name="sort_name",
     *     unique=true,
     *     nullable=true,
     *     options={"collation": "NOCASE"}
     * )
     */
    protected $sortName;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\RecipeIngredient",
     *     mappedBy="ingredient",
     *     orphanRemoval=true,
     *     cascade={"remove"}
     * )
     */
    protected $recipeIngredients;

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
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSortName()
    {
        return $this->sortName;
    }

    /**
     * @param string $sortName
     * @return $this
     */
    public function setSortName($sortName)
    {
        $this->sortName = $sortName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecipeIngredients()
    {
        return $this->recipeIngredients;
    }

    /**
     * @param mixed $recipeIngredients
     * @return $this
     */
    public function setRecipeIngredients($recipeIngredients) {
        $this->recipeIngredients = $recipeIngredients;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateSortName()
    {
        $this->sortName = Transliterator::sortNameTransliterator()->transliterate($this->name);
    }
}
