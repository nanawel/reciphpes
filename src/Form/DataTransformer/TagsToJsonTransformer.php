<?php


namespace App\Form\DataTransformer;


use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagsToJsonTransformer implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|array $tags
     * @return string JSON
     */
    public function transform($tags) {
        return json_encode($this->transformToArray($tags));
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|array $tags
     * @return array
     */
    public function transformToArray($tags) {
        if (! $tags) {
            return [];
        }
        if (! is_array($tags)) {
            $tags = $tags->toArray();
        }

        return array_reduce(
            $tags,
            function ($carry, $item) {
                return array_merge($carry, [['id' => $item->getId(), 'value' => $item->getName()]]);
            },
            []
        );
    }

    /**
     * @param string $json
     * @return array
     * @throws TransformationFailedException
     */
    public function reverseTransform($json) {
        $data = json_decode($json, JSON_OBJECT_AS_ARRAY);
        if (! $data) {
            return [];
        }

        $return = [];
        foreach ($data as $tagData) {
            if (! empty($tagData['id'])) {
                $return[] = $this->entityManager->getRepository(Tag::class)
                    ->find($tagData['id']);
            }
            else {
                $return[] = (new Tag())->setName($tagData['value']);
            }
        }

        return $return;
    }
}
