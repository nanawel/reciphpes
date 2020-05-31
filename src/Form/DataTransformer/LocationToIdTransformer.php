<?php


namespace App\Form\DataTransformer;


use App\Document\Location;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LocationToIdTransformer implements DataTransformerInterface
{
    /** @var DocumentManager */
    private $documentManager;

    public function __construct(DocumentManager $documentManager) {
        $this->documentManager = $documentManager;
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
        $location = $this->documentManager
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
