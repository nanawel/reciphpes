<?php


namespace App\Form\DataTransformer;


use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LocationToIdTransformer implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Location|null $location
     * @return string
     */
    public function transform($location) {
        if (null === $location) {
            return '';
        }

        return $location->getId();
    }

    /**
     * @param string $locationId
     * @return Location|null
     * @throws TransformationFailedException
     */
    public function reverseTransform($locationId) {
        if (! $locationId) {
            return null;
        }

        /** @var Location|null $location */
        $location = $this->entityManager
            ->getRepository(Location::class)
            ->find($locationId);

        if (null === $location) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid location ID: %s',
                    $locationId
                )
            );
        }

        return $location;
    }
}
