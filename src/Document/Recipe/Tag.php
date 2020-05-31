<?php

namespace App\Document\Recipe;

use App\Document\AbstractDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="Recipe_Tag", repositoryClass="App\Repository\Recipe\TagRepository")
 * @MongoDB\Index(keys={"id"="text"})
 */
class Tag extends AbstractDocument
{
    /** @MongoDB\Id(strategy="NONE", type="string") */
    public $id;

//    /** @MongoDB\Field(type="string") */
//    public $language = 'french';

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->getId();
    }

//    /**
//     * @return string
//     */
//    public function getLanguage() {
//        return $this->language;
//    }
}
