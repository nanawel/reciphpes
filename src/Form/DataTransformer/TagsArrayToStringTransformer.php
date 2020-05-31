<?php


namespace App\Form\DataTransformer;


use App\Document\Ingredient;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagsArrayToStringTransformer implements DataTransformerInterface
{
    /**
     * @param \Doctrine\Common\Collections\Collection $tags
     * @return string
     */
    public function transform($tags) {
        if (null === $tags) {
            return '';
        }

        return implode(', ', $tags);
    }

    /**
     * @param string $tags
     * @return array
     * @throws TransformationFailedException
     */
    public function reverseTransform($tags) {
        return array_map('trim', explode(',', $tags));
    }
}
