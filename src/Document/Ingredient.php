<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Dtc\GridBundle\Annotation as Grid;

/**
 * @MongoDB\Document
 */
class Ingredient
{
    /** @MongoDB\Id */
    public $id;

    /** @MongoDB\Field(type="string") */
    public $name;

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
}
