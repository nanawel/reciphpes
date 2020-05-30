<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MongoDB\Document(repositoryClass=App\Repository\RecipeRepository::class)
 */
class Recipe extends AbstractDocument
{
    /** @MongoDB\Id */
    public $id;

    /** @MongoDB\Field(type="string") @MongoDB\Index */
    public $name;

    /** @MongoDB\ReferenceMany(targetDocument=Location::class) */
    public $location;

    /** @MongoDB\ReferenceMany(targetDocument=Ingredient::class) */
    public $ingredients;

    /** @MongoDB\Field(type="string") */
    public $instructions;

    /** @MongoDB\Field(type="date") */
    public $createdAt;

    public function __construct() {
        $this->ingredients = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return Location
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @return ArrayCollection
     */
    public function getIngredients(): ArrayCollection {
        return $this->ingredients;
    }

    /**
     * @return mixed
     */
    public function getInstructions() {
        return $this->instructions;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }
}
