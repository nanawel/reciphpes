<?php

namespace App\Import;

use App\Entity\AbstractEntity;
use App\Entity\Registry;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use League\Csv\Reader;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class AbstractImport
{
    /**
     * Start collecting data for a new entity when this field is non-empty for a row.
     */
    const ENTITY_START_ROW_HEADER = 'name';

    /**
     * Use this field as key to determine if entity is new or existing.
     */
    const ENTITY_KEY = 'name';

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Registry */
    protected $entityRegistry;

    /** @var ClassMetadataInfo[] */
    protected $classMetadata = [];

    /** @var array */
    protected $associations = [];

    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    public function __construct(
        EntityManagerInterface $entityManager,
        Registry $entityRegistry,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->entityManager = $entityManager;
        $this->entityRegistry = $entityRegistry;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param string $csvFile
     * @param string $entityType
     * @param array $errors
     * @param array $options
     * @return bool
     */
    public function validate(string $csvFile, string $entityType, array &$errors, array $options = []): bool {
        $output = $options['output'] ?? new NullOutput();

        /** @var Reader $csv */
        $csv = Reader::createFromPath($csvFile);
        $csv->setHeaderOffset(0);

        $hasErrors = false;

        $progress = new ProgressBar($output);
        $progress->start();

        foreach ($this->getEntitiesData($csv->getRecords(), $entityType) as $entityRecordId => $entityData) {
            if ($entityData === null) {
                $errors[1][] = 'No entity row found.';
                $hasErrors = true;
            }
            else {
                $entityErrors = $this->validateEntityData($entityData, $entityType);
                $errors[$entityRecordId + 1] = $entityErrors;
                $hasErrors |= ! empty($entityErrors);
            }
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');

        return $hasErrors;
    }

    /**
     * @param \Iterator $it
     * @param string $entityType
     * @return \Generator
     */
    protected function getEntitiesData(\Iterator $it, string $entityType) {
        $currentEntityRecordId = null;
        $currentEntityData = null;
        foreach ($it as $recordId => $record) {
            if (! empty($record[static::ENTITY_START_ROW_HEADER])) {
                if (isset($currentEntityData)) {
                    yield $currentEntityRecordId => $currentEntityData;
                }
                $currentEntityRecordId = $recordId;
                $currentEntityData = $record;
            }
            else {
                $this->mergeEntityData($currentEntityData, $record, $entityType);
            }
        }
        // Don't forget to return the last entity (that has no successor hence not yielded in the loop)
        yield $currentEntityRecordId => $currentEntityData;
    }

    /**
     * @param array $entityData
     * @param array $recordData
     * @param string $entityType
     */
    protected function mergeEntityData(array &$entityData, array $recordData, string $entityType) {
        foreach ($recordData as $header => $recordDatum) {
            if (strlen(trim($recordDatum)) > 0) {
                if (! is_array($entityData[$header])) {
                    $entityData[$header] = [$entityData[$header]];
                }
                $entityData[$header][] = $recordDatum;
            }
        }
    }

    /**
     * @param array $entityData
     * @param string $entityType
     * @return array
     */
    protected function validateEntityData(array $entityData, string $entityType): array {
        // To be overridden

        return [];
    }

    public function import(string $csvFile, string $entityType, array $options = []) {
        $output = $options['output'] ?? new NullOutput();

        /** @var Reader $csv */
        $csv = Reader::createFromPath($csvFile);
        $csv->setHeaderOffset(0);

        // Turning off doctrine default logs queries for saving memory
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $batchSize = 20;
        $i = 1;

        $progress = new ProgressBar($output);
        $progress->start();

        foreach ($this->getEntitiesData($csv->getRecords(), $entityType) as $entityRecordId => $entityData) {
            $entity = $this->importEntity($entityData, $entityType);

            $this->entityManager->persist($entity);

            if (($i % $batchSize) === 0) {
                // Flush & clear (free used memory)
                $this->entityManager->flush();
                $this->entityManager->clear();

                $progress->advance($batchSize);
            }
            $i++;
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        // Ending the progress bar process
        $progress->advance(($i - 1) % $batchSize);
        $progress->finish();
    }

    /**
     * @param array $entityData
     * @param string $entityType
     * @return AbstractEntity
     */
    protected function importEntity(array $entityData, string $entityType): AbstractEntity {
        $entityClass = $this->entityRegistry->getEntityConfig($entityType, 'class');
        /** @var AbstractRepository $repository */
        $repository = $this->entityManager->getRepository($entityClass);

        $normData = $this->normalizeEntityData($entityData, $entityType);

        /** @var AbstractEntity|null $entity */
        $entity = $repository->findOneBy([self::ENTITY_KEY => $normData[self::ENTITY_KEY]]);

        if (! $entity) {
            $entity = new $entityClass();
        }

        foreach ($normData as $property => $value) {
            $this->setEntityProperty($entity, $property, $value);
        }

        return $entity;
    }

    /**
     * @param AbstractEntity $entity
     * @param string $property
     * @param mixed $value
     */
    protected function setEntityProperty(AbstractEntity $entity, string $property, $value) {
        $this->propertyAccessor->setValue($entity, $property, $value);
    }

    /**
     * @param array $entityData
     * @param string $entityType
     */
    protected function normalizeEntityData(array $entityData, string $entityType): array {
        foreach ($entityData as $field => $value) {
            $this->normalizeEntityFieldData($entityData, $field, $value, $entityType);
        }

        return $entityData;
    }

    /**
     * @param array $entityData
     * @param string $entityType
     */
    protected function normalizeEntityFieldData(array &$entityData, string $field, $value, string $entityType) {
        $associations = $this->getAssociations($this->entityRegistry->getEntityConfig($entityType, 'class'));

        $originalField = $field;
        if (strpos($originalField, '.') !== false) {
            if (substr_count($originalField, '.') > 1) {
                throw new \Exception('Only one dot max. is supported in field name: ' . $originalField);
            }
            list($field, $subField) = explode('.', $originalField);

            if (array_key_exists($field, $associations)) {
                if (! is_array($value)) {
                    $value = [$value];
                }
                if ($associations[$field]['type'] & ClassMetadataInfo::TO_MANY) {
                    for ($i = 0; $i < count($value); $i++) {
                        $this->setToManyAssociationFieldValue(
                            $entityData,
                            $field,
                            $i,
                            $subField,
                            $value[$i],
                            $associations[$field]
                        );
                    }
                }
                else {
                    $this->setToOneAssociationFieldValue(
                        $entityData,
                        $field,
                        $subField,
                        $value,
                        $associations[$field]
                    );
                }
            }
            unset($entityData[$originalField]);
        }
    }

    /**
     * @param array $entityData
     * @param string $field
     * @param string $subField
     * @param mixed $value
     * @param array $entityAssociations
     */
    protected function setToOneAssociationFieldValue(
        array &$entityData,
        string $field,
        string $subField,
        $value,
        array $entityAssociation
    ) {
        if (! array_key_exists($field, $entityData)) {
            $entityData[$field] = null;
        }
        $this->initTargetEntity($entityData[$field], $entityAssociation['targetEntity'], $subField, $value[0]);

        $this->propertyAccessor->setValue($entityData[$field], $subField, $value[0]);
    }

    /**
     * @param array $entityData
     * @param string $field
     * @param string $subField
     * @param mixed $value
     * @param array $entityAssociation
     */
    protected function setToManyAssociationFieldValue(
        array &$entityData,
        string $field,
        int $idx,
        string $subField,
        $value,
        array $entityAssociation
    ) {
        if (! array_key_exists($idx, $entityData[$field] ?? [])) {
            $entityData[$field][$idx] = null;
        }
        $this->initTargetEntity($entityData[$field][$idx], $entityAssociation['targetEntity'], $subField, $value);

        $this->propertyAccessor->setValue($entityData[$field][$idx], $subField, $value);
    }

    protected function initTargetEntity(&$reference, string $class, string $keyField, $keyValue) {
        if (! isset($reference)) {
            // Use the first field from imported data to find target entity if it exists
            $reference = $this->entityManager->getRepository($class)
                ->findOneBy([$keyField => $keyValue]);

            if (! $reference) {
                $reference = new $class();
            }
        }
    }

    /**
     * @param string $className
     * @return ClassMetadataInfo
     */
    protected function getClassMetadataInfo(string $className): ClassMetadataInfo {
        if (! isset($this->classMetadata[$className])) {
            $this->classMetadata[$className] = $this->entityManager->getClassMetadata($className);
        }

        return $this->classMetadata[$className];
    }

    /**
     * @param string $className
     * @return array
     */
    protected function getAssociations(string $className): array {
        if (! isset($this->associations[$className])) {
            $this->associations[$className] = $this->getClassMetadataInfo($className)->getAssociationMappings();
        }

        return $this->associations[$className];
    }
}
