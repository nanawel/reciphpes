<?php

namespace App\Document\Recipe;

use App\Document\AbstractDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Tag extends AbstractDocument
{
    /** @MongoDB\Id(strategy="NONE", type="string") */
    public $name;

    /** @MongoDB\Field(type="date") */
    public $createdAt;

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
