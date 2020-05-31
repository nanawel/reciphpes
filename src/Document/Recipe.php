<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\Collection;

/**
 * @MongoDB\Document(collection="Recipe", repositoryClass=App\Repository\RecipeRepository::class)
 * @MongoDB\Index(keys={"name"="text", "tags"="text", "instructions"="text"})
 */
class Recipe extends AbstractDocument
{
    /** @MongoDB\Id */
    public $id;

    /** @MongoDB\Field(type="string") @MongoDB\Index */
    public $name;

    /** @MongoDB\Field(type="collection") @MongoDB\Index */
    public $tags;

    /** @MongoDB\ReferenceOne(targetDocument=Location::class, storeAs="id") */
    public $location;

    /** @MongoDB\Field(type="string") */
    public $locationDetails;

    /** @MongoDB\ReferenceMany(targetDocument=Ingredient::class, storeAs="id") */
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
     * @return array
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * @return Location
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getLocationDetails() {
        return $this->locationDetails;
    }

    /**
     * @return Collection
     */
    public function getIngredients(): Collection {
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