<?php

namespace App\Document;

use App\Repository\RecipeRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MongoDB\Document
 */
class Recipe
{
    /** @MongoDB\Id */
    public $id;

    /** @MongoDB\Field(type="string") */
    public $name;

    /** @MongoDB\ReferenceMany(targetDocument=Location::class) */
    public $location;

    /** @MongoDB\ReferenceMany(targetDocument=Ingredient::class) */
    public $ingredients;

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
}
