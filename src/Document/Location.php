<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="Location", repositoryClass=App\Repository\LocationRepository::class)
 * @MongoDB\Index(keys={"name"="text"})
 */
class Location extends AbstractDocument
{
    /** @MongoDB\Id */
    public $id;

    /** @MongoDB\Field(type="string") */
    public $name;

    /** @MongoDB\Field(type="date") */
    public $createdAt;

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
     * @return mixed
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }
}
