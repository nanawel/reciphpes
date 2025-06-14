<?php


namespace App\Form\DataTransformer;


use App\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagsToJsonTransformer implements DataTransformerInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|array $tags
     * @return string JSON
     */
    public function transform($tags)
    {
        return json_encode($this->transformToArray($tags));
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|array $tags
     */
    public function transformToArray($tags): array
    {
        if (!$tags) {
            return [];
        }

        if (!is_array($tags)) {
            $tags = $tags->toArray();
        }

        return array_reduce(
            $tags,
            fn($carry, $item) => array_merge($carry, [['id' => $item->getId(), 'value' => $item->getName()]]),
            []
        );
    }

    /**
     * @param string $json
     * @return Collection
     * @throws TransformationFailedException
     */
    public function reverseTransform($json) {
        $return = new ArrayCollection();
        $data = json_decode($json, JSON_OBJECT_AS_ARRAY);
        if (! $data) {
            return $return;
        }

        foreach ($data as $tagData) {
            if (! empty($tagData['id'])) {
                $return->add(
                    $this->entityManager->getRepository(Tag::class)->find($tagData['id'])
                );
            }
            else {
                $return->add((new Tag())->setName($tagData['value']));
            }
        }

        return $return;
    }
}
