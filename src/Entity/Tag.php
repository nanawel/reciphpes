<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(
 *     repositoryClass=App\Repository\TagRepository::class
 * )
 * @ORM\Table(
 *     name="tag",
 *     indexes={
 *         @ORM\Index(name="TAG_NAME_IDX", columns={"name"})
 *     }
 * )
 */
class Tag extends AbstractEntity
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
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): static
    {
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
     */
    public function setName($name): static
    {
        if (trim((string)$name) === '') {
            throw new \InvalidArgumentException('A tag cannot have an empty name.');
        }

        // FIXME Need to force case here as I could not find how to do it in SQLite with Doctrine
        $this->name = strtolower((string)$name);

        return $this;
    }
}
